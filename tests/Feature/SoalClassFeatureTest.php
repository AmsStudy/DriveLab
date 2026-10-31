<?php

namespace Tests\Feature;

use App\Models\Soal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SoalClassFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_soal_with_classes(): void
    {
        $soalData = [
            'title'                   => 'Tugas Jaringan Komputer',
            'description'             => 'Implementasi VLAN dan Trunking',
            'kode_unik'               => 'NET-001',
            'password'                => 'rahasia123',
            'access_type'             => 'lifetime',
            'is_submission_enabled'   => '1',
            'submission_types'        => ['file', 'link', 'text'],
            'deadline_at'             => now()->addDays(3)->format('Y-m-d\TH:i'),
            'allowed_extensions'      => ['all'],
            'classes'                 => 'A1, A2, B1, b2, a1', // Duplicates and lowercase should be handled
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.soal.store'), $soalData);
        $response->assertSessionHas('success');

        $soal = Soal::where('kode_unik', 'NET-001')->first();
        $this->assertNotNull($soal);
        $this->assertTrue($soal->hasClasses());
        $this->assertEquals(['A1', 'A2', 'B1', 'B2'], $soal->classes);
    }

    public function test_admin_can_update_soal_classes(): void
    {
        $soal = Soal::create([
            'title'                 => 'Tugas Basis Data',
            'kode_unik'             => 'DB-001',
            'unique_token'          => 'db1234',
            'password'              => Hash::make('rahasia123'),
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['file'],
            'allowed_extensions'    => ['pdf'],
            'classes'               => ['A1', 'A2'],
        ]);

        $updateData = [
            'title'                 => 'Tugas Basis Data Update',
            'kode_unik'             => 'DB-001',
            'access_type'           => 'lifetime',
            'is_submission_enabled' => '1',
            'deadline_at'           => now()->addDays(5)->format('Y-m-d\TH:i'),
            'submission_types'      => ['file'],
            'allowed_extensions'    => ['pdf'],
            'classes'               => 'C1, C2, C3',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.soal.update', $soal), $updateData);
        $response->assertSessionHas('success');

        $soal->refresh();
        $this->assertEquals(['C1', 'C2', 'C3'], $soal->classes);
    }

    public function test_student_must_select_class_if_soal_has_classes(): void
    {
        $soal = Soal::create([
            'title'                 => 'Tugas Pemrograman Web',
            'kode_unik'             => 'WEB-001',
            'unique_token'          => 'web123',
            'password'              => Hash::make('rahasia123'),
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['link'],
            'classes'               => ['A1', 'A2', 'B1'],
        ]);

        // Submit without class
        $response = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->postJson(route('soal.submitTask', $soal->unique_token), [
                'nim'             => '13020230001',
                'nama'            => 'Budi Pratama',
                'submission_type' => 'link',
                'link_url'        => 'https://github.com/budi/web-project',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['kelas']);

        // Submit with invalid class
        $invalidResponse = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->postJson(route('soal.submitTask', $soal->unique_token), [
                'nim'             => '13020230001',
                'nama'            => 'Budi Pratama',
                'kelas'           => 'Z9', // Not in ['A1', 'A2', 'B1']
                'submission_type' => 'link',
                'link_url'        => 'https://github.com/budi/web-project',
            ]);

        $invalidResponse->assertStatus(422);
        $invalidResponse->assertJsonValidationErrors(['kelas']);
    }

    public function test_student_can_submit_with_valid_class(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'title'                 => 'Tugas Pemrograman Mobile',
            'kode_unik'             => 'MOB-001',
            'unique_token'          => 'mob123',
            'password'              => Hash::make('rahasia123'),
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['file'],
            'allowed_extensions'    => ['pdf'],
            'classes'               => ['A1', 'A2', 'B1'],
        ]);

        $file = UploadedFile::fake()->create('laporan.pdf', 500, 'application/pdf');

        $response = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->postJson(route('soal.submitTask', $soal->unique_token), [
                'nim'             => '13020230088',
                'nama'            => 'Andi Wijaya',
                'kelas'           => 'A1',
                'submission_type' => 'file',
                'file'            => $file,
            ]);

        $response->assertStatus(200);

        $submission = Submission::where('soal_id', $soal->id)->where('nim', '13020230088')->first();
        $this->assertNotNull($submission);
        $this->assertEquals('A1', $submission->kelas);
        $this->assertStringStartsWith('submissions/' . $soal->id . '/A1_13020230088_Andi_Wijaya_', $submission->file_path);
    }

    public function test_admin_can_download_zip_filtered_by_class(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'title'                 => 'Tugas Algoritma',
            'kode_unik'             => 'ALGO-001',
            'unique_token'          => 'algo123',
            'password'              => Hash::make('rahasia123'),
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['link'],
            'classes'               => ['A1', 'B1'],
        ]);

        // Submission kelas A1
        Submission::create([
            'soal_id'         => $soal->id,
            'nim'             => '13020230011',
            'nama'            => 'Mahasiswa A1',
            'kelas'           => 'A1',
            'submission_type' => 'link',
            'link_url'        => 'https://github.com/mhs/a1',
        ]);

        // Submission kelas B1
        Submission::create([
            'soal_id'         => $soal->id,
            'nim'             => '13020230022',
            'nama'            => 'Mahasiswa B1',
            'kelas'           => 'B1',
            'submission_type' => 'link',
            'link_url'        => 'https://github.com/mhs/b1',
        ]);

        // Download filtered by A1
        $responseA1 = $this->actingAs($this->admin)->get(route('admin.soal.download-submissions-zip', [$soal, 'kelas' => 'A1']));
        $responseA1->assertStatus(200);
        $this->assertStringContainsString('kelas_a1', $responseA1->headers->get('content-disposition'));

        // Download all classes
        $responseAll = $this->actingAs($this->admin)->get(route('admin.soal.download-submissions-zip', $soal));
        $responseAll->assertStatus(200);
        $this->assertStringContainsString('semua_kelas', $responseAll->headers->get('content-disposition'));
    }

    public function test_admin_can_export_csv_with_class_column(): void
    {
        $soal = Soal::create([
            'title'                 => 'Tugas Rekayasa Perangkat Lunak',
            'kode_unik'             => 'RPL-001',
            'unique_token'          => 'rpl123',
            'password'              => Hash::make('rahasia123'),
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'submission_types'      => ['link'],
            'classes'               => ['A1', 'A2'],
        ]);

        Submission::create([
            'soal_id'         => $soal->id,
            'nim'             => '13020230099',
            'nama'            => 'Citra Lestari',
            'kelas'           => 'A2',
            'submission_type' => 'link',
            'link_url'        => 'https://github.com/citra/rpl',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.soal.export-submissions', [$soal, 'kelas' => 'A2']));
        $response->assertStatus(200);

        $content = $response->streamedContent();
        $this->assertStringContainsString('Kelas', $content);
        $this->assertStringContainsString('A2', $content);
        $this->assertStringContainsString('Citra Lestari', $content);
    }
}
