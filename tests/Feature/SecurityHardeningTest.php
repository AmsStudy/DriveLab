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
}
