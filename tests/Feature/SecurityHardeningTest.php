<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_access_by_code_rate_limiting(): void
    {
        // 10 percobaaan pertama kode salah
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/akses-kode', ['kode_unik' => 'INVALID-CODE-' . $i]);
            $response->assertSessionHas('error', 'Kode unik dokumen tidak ditemukan.');
        }

        // Percobaan ke-11 harus diblokir oleh rate limiter
        $response = $this->post('/akses-kode', ['kode_unik' => 'INVALID-CODE-11']);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('error'));
    }

    public function test_lab_environment_rate_limiting_is_isolated_per_client_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $soal = Soal::create([
            'title'          => 'Ujian Lab Komputer',
            'kode_unik'      => 'LAB-KOMP-01',
            'unique_token'   => 'token-lab-01',
            'password'       => Hash::make('password123'),
            'plain_password' => 'password123',
            'user_id'        => $admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
        ]);

        // Komputer A di lab (Session A) gagal input password 5 kali
        for ($i = 0; $i < 5; $i++) {
            $resA = $this->withHeader('X-Test-Session-Id', 'computer_pc_01')
                ->post(route('soal.verify', ['token' => $soal->unique_token]), [
                    'password' => 'wrong_pass',
                ]);
            $resA->assertSessionHasErrors(['password']);
        }

        // Komputer A pada percobaan ke-6 harus diblokir oleh cooldown rate limiter
        $blockedA = $this->withHeader('X-Test-Session-Id', 'computer_pc_01')
            ->post(route('soal.verify', ['token' => $soal->unique_token]), [
                'password' => 'wrong_pass',
            ]);
        $blockedA->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('password'));

        // Komputer B di lab yang sama (Session B, IP sama) harus TETAP BISA mengakses tanpa terkena blokir
        $resB = $this->withHeader('X-Test-Session-Id', 'computer_pc_02')
            ->post(route('soal.verify', ['token' => $soal->unique_token]), [
                'password' => 'password123',
            ]);

        // Komputer B berhasil diarahkan ke soal.view
        $resB->assertRedirect(route('soal.view', ['token' => $soal->unique_token]));
    }

    public function test_admin_file_upload_blocks_dangerous_scripts(): void
    {
        $admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name'       => 'Jaringan',
            'slug'       => 'jaringan',
            'created_by' => $admin->id,
        ]);

        // Fake file script php
        $dangerousFile = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

        $response = $this->actingAs($admin)->post(route('admin.soal.store'), [
            'title'       => 'Test Soal Aman',
            'kode_unik'   => 'AMSSOAL-TEST1',
            'password'    => 'secret123',
            'category_id' => $category->id,
            'access_type' => 'lifetime',
            'files'       => [$dangerousFile],
        ]);

        $response->assertSessionHasErrors('files');
        $this->assertDatabaseMissing('soals', ['kode_unik' => 'AMSSOAL-TEST1']);
    }

    public function test_submission_rejects_unsafe_url_schemes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $soal = Soal::create([
            'title'                 => 'Soal Link Test',
            'kode_unik'             => 'AMSSOAL-LINK1',
            'unique_token'          => 'token123',
            'password'              => Hash::make('password123'),
            'user_id'               => $admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['link'],
            'deadline_at'           => now()->addDays(5),
            'allowed_extensions'    => ['all'],
        ]);

        // Simulasikan session akses yang sah
        session(["soal_access_token123" => true]);

        // Kirim link dengan skema javascript/ftp
        $response = $this->postJson(route('soal.submitTask', ['token' => 'token123']), [
            'submission_type' => 'link',
            'nim'             => '12345678',
            'nama'            => 'Budi Test',
            'link_url'        => 'javascript:alert(1)',
        ]);

        $response->assertStatus(422);
    }

    public function test_export_submissions_sanitizes_csv_formula_injection(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $soal = Soal::create([
            'title'                 => 'Soal Formula Test',
            'kode_unik'             => 'AMSSOAL-FORM1',
            'unique_token'          => 'tokenform1',
            'password'              => Hash::make('password123'),
            'user_id'               => $admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['text'],
            'deadline_at'           => now()->addDays(5),
            'allowed_extensions'    => ['all'],
        ]);

        // Simpan submission dengan formula berbahaya (=cmd|'/C calc'!A0)
        \App\Models\Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '2021001',
            'nama'               => '=cmd|\'/C calc\'!A0',
            'submission_type'    => 'text',
            'text_content'       => '@SUM(1,2)',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.soal.export-submissions', $soal));
        $response->assertStatus(200);

        $content = $response->streamedContent();

        // Pastikan diawali dengan petik tunggal (') sehingga tidak dieksekusi Excel
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'@SUM(1,2)", $content);
    }

    public function test_csrf_refresh_endpoint_returns_json_token(): void
    {
        $response = $this->getJson(route('csrf.refresh'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['csrf_token']);
        $this->assertNotEmpty($response->json('csrf_token'));
    }
}
