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
use ZipArchive;

class SoalMultipleFilesAndZipStructureTest extends TestCase
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

    public function test_admin_can_set_max_files_when_creating_soal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.soal.store'), [
            'title'                 => 'Tugas Sistem Operasi',
            'kode_unik'             => 'OS-001',
            'password'              => 'admin123',
            'access_type'           => 'lifetime',
            'is_submission_enabled' => '1',
            'submission_types'      => ['file'],
            'max_files'             => 4,
            'allowed_extensions'    => ['pdf', 'word'],
            'deadline_at'           => now()->addDays(5)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.soal.index'));

        $soal = Soal::where('kode_unik', 'OS-001')->first();
        $this->assertNotNull($soal);
        $this->assertEquals(4, $soal->max_files);
        $this->assertEquals(4, $soal->getMaxFiles());
    }

    public function test_admin_can_update_max_files(): void
    {
        $soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Tugas Pemrograman Web',
            'kode_unik'             => 'WEB-001',
            'unique_token'          => 'web123',
            'password'              => Hash::make('admin123'),
            'plain_password'        => 'admin123',
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'max_files'             => 1,
            'allowed_extensions'    => ['all'],
            'deadline_at'           => now()->addDays(5),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.soal.update', $soal), [
            'title'                 => 'Tugas Pemrograman Web Updated',
            'kode_unik'             => 'WEB-001',
            'access_type'           => 'lifetime',
            'is_submission_enabled' => '1',
            'submission_types'      => ['file'],
            'max_files'             => 5,
            'allowed_extensions'    => ['all'],
            'deadline_at'           => now()->addDays(6)->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.soal.show', $soal));
        $this->assertEquals(5, $soal->fresh()->max_files);
    }

    public function test_student_can_submit_multiple_files_up_to_max_files(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Tugas Jaringan',
            'kode_unik'             => 'NET-002',
            'unique_token'          => 'net456',
            'password'              => Hash::make('mhs123'),
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'max_files'             => 3,
            'allowed_extensions'    => ['all'],
        ]);

        $file1 = UploadedFile::fake()->create('laporan.pdf', 100);
        $file2 = UploadedFile::fake()->create('topologi.pkt', 150);
        $file3 = UploadedFile::fake()->create('screenshot.png', 80);

        $response = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->postJson(route('soal.submitTask', $soal->unique_token), [
                'nim'             => '13020230001',
                'nama'            => 'Budi Santoso',
                'submission_type' => 'file',
                'files'           => [$file1, $file2, $file3],
            ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Tugas berhasil dikumpulkan (3 berkas).',
        ]);

        $submission = Submission::with('files')
            ->where('soal_id', $soal->id)
            ->where('nim', '13020230001')
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(3, $submission->getFilesCount());
        $this->assertCount(3, $submission->files);
    }

    public function test_student_cannot_exceed_max_files(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Tugas Basis Data',
            'kode_unik'             => 'DB-002',
            'unique_token'          => 'db789',
            'password'              => Hash::make('mhs123'),
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'max_files'             => 2,
            'allowed_extensions'    => ['all'],
        ]);

        $files = [
            UploadedFile::fake()->create('file1.pdf', 50),
            UploadedFile::fake()->create('file2.pdf', 50),
            UploadedFile::fake()->create('file3.pdf', 50),
        ];

        $response = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->postJson(route('soal.submitTask', $soal->unique_token), [
                'nim'             => '13020230002',
                'nama'            => 'Ahmad Dahlan',
                'submission_type' => 'file',
                'files'           => $files,
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('Jumlah berkas melebihi batas maksimal', $response->json('message'));
    }

    public function test_bulk_download_contains_folder_per_student_inside_single_zip(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Praktikum Algoritma',
            'kode_unik'             => 'ALGO-001',
            'unique_token'          => 'algo111',
            'password'              => Hash::make('admin123'),
            'plain_password'        => 'admin123',
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'max_files'             => 3,
            'classes'               => ['A', 'B'],
        ]);

        // Student 1 (Class A, 2 files)
        $f1 = UploadedFile::fake()->create('tugas1.pdf', 100);
        $f2 = UploadedFile::fake()->create('kode.zip', 200);
        $path1 = $f1->storeAs('submissions/' . $soal->id, 'file1.pdf', 'private');
        $path2 = $f2->storeAs('submissions/' . $soal->id, 'file2.zip', 'private');

        $sub1 = Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020230010',
            'nama'               => 'Budi Pratama',
            'kelas'              => 'A',
            'submission_type'    => 'file',
            'file_path'          => $path1,
            'file_original_name' => 'tugas1.pdf',
        ]);
        $sub1->files()->create(['file_path' => $path1, 'file_original_name' => 'tugas1.pdf', 'file_size' => 100]);
        $sub1->files()->create(['file_path' => $path2, 'file_original_name' => 'kode.zip', 'file_size' => 200]);

        // Student 2 (Class B, 1 file)
        $f3 = UploadedFile::fake()->create('jawaban.pdf', 150);
        $path3 = $f3->storeAs('submissions/' . $soal->id, 'file3.pdf', 'private');

        $sub2 = Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020230020',
            'nama'               => 'Siti Aisyah',
            'kelas'              => 'B',
            'submission_type'    => 'file',
            'file_path'          => $path3,
            'file_original_name' => 'jawaban.pdf',
        ]);
        $sub2->files()->create(['file_path' => $path3, 'file_original_name' => 'jawaban.pdf', 'file_size' => 150]);

        $response = $this->actingAs($this->admin)->get(route('admin.soal.download-submissions-zip', $soal));
        $response->assertOk();

        // Verify ZIP contents
        $zipFile = $response->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipFile));

        $entryNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryNames[] = $zip->getNameIndex($i);
        }
        $zip->close();

        // Check that each student has their own folder!
        $this->assertContains('Kelas_A/13020230010_Budi_Pratama/tugas1.pdf', $entryNames);
        $this->assertContains('Kelas_A/13020230010_Budi_Pratama/kode.zip', $entryNames);
        $this->assertContains('Kelas_B/13020230020_Siti_Aisyah/jawaban.pdf', $entryNames);
    }

    public function test_individual_student_download_downloads_zip_archive(): void
    {
        Storage::fake('private');

        $soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Praktikum Struktur Data',
            'kode_unik'             => 'STRUK-001',
            'unique_token'          => 'struk222',
            'password'              => Hash::make('admin123'),
            'plain_password'        => 'admin123',
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'max_files'             => 2,
        ]);

        $f1 = UploadedFile::fake()->create('laporan.docx', 120);
        $f2 = UploadedFile::fake()->create('program.py', 50);
        $p1 = $f1->storeAs('submissions/' . $soal->id, 'doc1.docx', 'private');
        $p2 = $f2->storeAs('submissions/' . $soal->id, 'py1.py', 'private');

        $sub = Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020230099',
            'nama'               => 'Rian Firmansyah',
            'submission_type'    => 'file',
            'file_path'          => $p1,
            'file_original_name' => 'laporan.docx',
        ]);
        $sub->files()->create(['file_path' => $p1, 'file_original_name' => 'laporan.docx', 'file_size' => 120]);
        $sub->files()->create(['file_path' => $p2, 'file_original_name' => 'program.py', 'file_size' => 50]);

        $response = $this->actingAs($this->admin)->get(route('admin.soal.download-submissions', $sub->id));
        $response->assertOk();

        // Check content-disposition contains .zip
        $disposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('.zip', $disposition);
        $this->assertStringContainsString('tugas_13020230099_Rian_Firmansyah.zip', $disposition);

        // Verify ZIP contents
        $zipFile = $response->getFile()->getPathname();
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipFile));

        $entryNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryNames[] = $zip->getNameIndex($i);
        }
        $zip->close();

        $this->assertContains('laporan.docx', $entryNames);
        $this->assertContains('program.py', $entryNames);
    }
}
