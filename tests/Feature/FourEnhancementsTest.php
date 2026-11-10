<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\Soal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FourEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * Point 1: Halaman akses soal tidak aktif menampilkan cyber standby card & sinkronisasi otomatis.
     */
    public function test_inactive_soal_access_renders_standby_card_and_poll_elements(): void
    {
        $soal = Soal::create([
            'title'          => 'Praktikum Sistem Operasi',
            'slug'           => 'praktikum-sistem-operasi',
            'kode_unik'      => 'SO-MODUL-1',
            'unique_token'   => 'token-soal-inactive-01',
            'password'       => bcrypt('secret123'),
            'plain_password' => 'secret123',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => false, // Inactive
        ]);

        $response = $this->get(route('soal.access', ['token' => $soal->slug]));
        $response->assertOk();
        $response->assertSee('Akses Dokumen Sedang Dijeda');
        $response->assertSee('STANDBY');
        $response->assertSee('btn-check-status');
        $response->assertSee('checkStatusNow');
    }

    /**
     * Point 2: Endpoint status kuis publik mengembalikan JSON dan lobby kuis inaktif menampilkan polling otomatis.
     */
    public function test_public_quiz_status_endpoint_and_inactive_lobby_poll(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Jaringan Komputer',
            'slug'               => 'kuis-jaringan-komputer',
            'access_code'        => 'LAB-JK01',
            'unique_token'       => 'token-quiz-test-01',
            'time_limit_minutes' => 15,
            'is_active'          => false,
            'session_status'     => 'waiting',
        ]);

        // Cek JSON status endpoint
        $statusResponse = $this->get(route('quiz.status', ['token' => $quiz->slug]));
        $statusResponse->assertOk();
        $statusResponse->assertJson([
            'is_active'      => false,
            'session_status' => 'waiting',
            'title'          => 'Kuis Jaringan Komputer',
        ]);

        // Cek halaman lobby
        $lobbyResponse = $this->get(route('quiz.lobby', ['token' => $quiz->slug]));
        $lobbyResponse->assertOk();
        $lobbyResponse->assertSee('Sesi Kuis Belum Dibuka');
        $lobbyResponse->assertSee('STANDBY');
        $lobbyResponse->assertSee('btn-quiz-check');
        $lobbyResponse->assertSee('checkQuizStatusNow');
    }

    /**
     * Point 3: Mahasiswa dapat memeriksa berkas tugas miliknya (digital receipt) dan mengunduh berkasnya sendiri.
     */
    public function test_student_can_check_submission_receipt_and_download_own_submission(): void
    {
        $soal = Soal::create([
            'title'                 => 'Pemrograman Web',
            'slug'                  => 'pemrograman-web',
            'kode_unik'             => 'WEB-MODUL-1',
            'unique_token'          => 'token-web-01',
            'password'              => bcrypt('web123'),
            'plain_password'        => 'web123',
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
        ]);

        // Berikan sesi akses ke soal
        session(["soal_access_{$soal->unique_token}" => true]);

        // Simpan file tugas fisik dummy di disk private
        $uploaded = UploadedFile::fake()->create('laporan_web.pdf', 250);
        $storedPath = $uploaded->storeAs('submissions/' . $soal->id, 'laporan_web.pdf', 'private');

        $submission = Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020230001',
            'nama'               => 'Budi Santoso',
            'kelas'              => 'A1',
            'submission_type'    => 'file',
            'file_path'          => $storedPath,
            'file_original_name' => 'laporan_web.pdf',
            'file_size'          => 256000,
        ]);

        $subFile = $submission->files()->create([
            'file_path'          => $storedPath,
            'file_original_name' => 'laporan_web.pdf',
            'file_extension'     => 'pdf',
            'file_size'          => 256000,
        ]);

        // 1. Cek endpoint checkSubmission
        $checkResponse = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->get(route('soal.checkSubmission', [
                'token' => $soal->slug,
                'nim'   => '13020230001',
            ]));

        $checkResponse->assertOk();
        $checkResponse->assertJson([
            'has_submitted'   => true,
            'nim'             => '13020230001',
            'nama'            => 'Budi Santoso',
            'submission_type' => 'file',
        ]);

        $checkData = $checkResponse->json();
        $this->assertNotEmpty($checkData['files']);
        $this->assertEquals('laporan_web.pdf', $checkData['files'][0]['name']);
        $downloadUrl = $checkData['files'][0]['download_url'];

        // 2. Mahasiswa mengunduh berkasnya sendiri
        $downloadResponse = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->get($downloadUrl);

        $downloadResponse->assertOk();
        $downloadResponse->assertHeader('content-disposition', 'attachment; filename=laporan_web.pdf');
    }

    /**
     * Point 3 & Point 4: Proteksi akses download berkas tanpa otorisasi password & render banner offline.
     */
    public function test_unauthorized_download_blocked_and_offline_banner_rendered(): void
    {
        $soal = Soal::create([
            'title'                 => 'Basis Data',
            'slug'                  => 'basis-data',
            'kode_unik'             => 'BD-MODUL-1',
            'unique_token'          => 'token-bd-01',
            'password'              => bcrypt('bd123'),
            'plain_password'        => 'bd123',
            'user_id'               => $this->admin->id,
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
        ]);

        // Akses download submission tanpa sesi password harus ditolak 403
        $downloadResponse = $this->get(route('soal.downloadOwnSubmission', [
            'token'  => $soal->slug,
            'fileId' => 1,
            'nim'    => '13020230001',
        ]));
        $downloadResponse->assertStatus(403);

        // Halaman view menampilkan banner offline dan container digital receipt
        $viewResponse = $this->withSession(["soal_access_{$soal->unique_token}" => true])
            ->get(route('soal.view', ['token' => $soal->slug]));

        $viewResponse->assertOk();
        $viewResponse->assertSee('id="offline-connection-banner"', false);
        $viewResponse->assertSee('KONEKSI LAB TERPUTUS');
        $viewResponse->assertSee('id="existing-sub-files-list"', false);
    }
}

