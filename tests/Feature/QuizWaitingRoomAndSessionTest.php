<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizWaitingRoomAndSessionTest extends TestCase
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

    public function test_student_enters_waiting_room_when_quiz_session_is_waiting(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Ujian Jaringan Komputer',
            'session_status'     => 'waiting',
            'time_limit_minutes' => 30,
            'is_active'          => true,
            'max_attempts'       => 1,
        ]);

        $q = $quiz->questions()->create([
            'question_text' => 'Berapa jumlah port TCP?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
        ]);
        $q->options()->create(['option_text' => '65535', 'is_correct' => true, 'order_num' => 1]);

        $response = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '13020210001',
            'nama' => 'Muhammad Rifqi',
        ]);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('nim', '13020210001')->first();
        $this->assertNotNull($attempt);

        // Harus masuk ke Ruang Tunggu (waiting room), BUKAN langsung ke play!
        $response->assertRedirect(route('quiz.waiting', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        // Render halaman ruang tunggu
        $waitingRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.waiting', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $waitingRes->assertStatus(200);
        $waitingRes->assertSee('Muhammad Rifqi');
        $waitingRes->assertSee('13020210001');
        $waitingRes->assertSee('Ruang Tunggu Ujian Aktif');
        $waitingRes->assertSee('Ujian Jaringan Komputer');
    }

    public function test_student_cannot_bypass_waiting_room_by_direct_play_url(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Ujian Sistem Operasi',
            'session_status'     => 'waiting',
            'time_limit_minutes' => 20,
            'is_active'          => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '13020210002',
            'student_name'          => 'Andi Dian',
            'total_points_possible' => 100,
            'started_at'            => now(),
        ]);

        // Coba akses langsung play arena saat quiz masih di ruang tunggu
        $playRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        // Harus dicegat dan dialihkan kembali ke ruang tunggu!
        $playRes->assertRedirect(route('quiz.waiting', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
    }

    public function test_waiting_status_endpoint_returns_waiting_then_started_upon_admin_start(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Algoritma',
            'session_status'     => 'waiting',
            'time_limit_minutes' => 15,
            'is_active'          => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '13020210003',
            'student_name'          => 'Siti Fatimah',
            'total_points_possible' => 50,
            'started_at'            => now()->subMinutes(10), // misal gabung 10 menit lalu
        ]);

        // 1. Polling saat masih waiting
        $statusRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->getJson(route('quiz.waiting.status', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $statusRes->assertStatus(200);
        $statusRes->assertJson([
            'session_status' => 'waiting',
            'redirect'       => null,
            'waiting_count'  => 1,
        ]);

        // 2. Admin menekan Mulai Kuis
        $startSessionRes = $this->actingAs($this->admin)->postJson(route('admin.quizzes.session.start', $quiz));
        $startSessionRes->assertStatus(200);
        $startSessionRes->assertJson([
            'success'        => true,
            'session_status' => 'started',
        ]);

        $quiz->refresh();
        $this->assertEquals('started', $quiz->session_status);
        $this->assertTrue($quiz->isSessionStarted());

        // Pastikan timer mahasiswa direset ke sekarang (started_at = now()) agar tidak terpotong waktu tunggu
        $attempt->refresh();
        $this->assertTrue(abs(now()->diffInSeconds($attempt->started_at)) < 5);

        // 3. Polling kembali oleh mahasiswa: otomatis mendeteksi started dan memberi redirect URL!
        $statusAfterStart = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->getJson(route('quiz.waiting.status', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $statusAfterStart->assertStatus(200);
        $statusAfterStart->assertJson([
            'session_status' => 'started',
            'redirect'       => route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]),
        ]);

        // 4. Mahasiswa sekarang bisa masuk ke arena soal
        $playRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $playRes->assertStatus(200);
    }

    public function test_late_student_directly_enters_play_when_session_already_started(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Ujian Basis Data Lanjut',
            'session_status'     => 'started', // Sudah dimulai admin
            'time_limit_minutes' => 30,
            'is_active'          => true,
        ]);

        $res = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '13020210009',
            'nama' => 'Mahasiswa Terlambat',
        ]);

        $attempt = QuizAttempt::where('nim', '13020210009')->first();
        $this->assertNotNull($attempt);

        // Karena sesi kuis sudah 'started', langsung dialihkan ke play arena
        $res->assertRedirect(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
    }

    public function test_admin_can_end_quiz_session_and_finalizes_all_active_attempts(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Ujian Pemrograman Berorientasi Objek',
            'session_status'     => 'started',
            'time_limit_minutes' => 60,
            'is_active'          => true,
        ]);

        $q = $quiz->questions()->create([
            'question_text' => 'Konsep OOP?',
            'question_type' => 'multiple_choice',
            'points'        => 100,
        ]);
        $opt = $q->options()->create(['option_text' => 'Inheritance', 'is_correct' => true, 'order_num' => 1]);

        $att1 = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '13020210010',
            'student_name'          => 'Budi Haryanto',
            'total_points_possible' => 100,
            'started_at'            => now(),
            'draft_answers'         => [$q->id => $opt->id],
        ]);

        $att2 = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '13020210011',
            'student_name'          => 'Citra Amelia',
            'total_points_possible' => 100,
            'started_at'            => now(),
            'draft_answers'         => [],
        ]);

        // Admin menekan Hentikan Kuis
        $endRes = $this->actingAs($this->admin)->postJson(route('admin.quizzes.session.end', $quiz));
        $endRes->assertStatus(200);
        $endRes->assertJson([
            'success'        => true,
            'session_status' => 'ended',
        ]);

        $quiz->refresh();
        $this->assertEquals('ended', $quiz->session_status);
        $this->assertTrue($quiz->isSessionEnded());

        // Kedua mahasiswa otomatis diselesaikan dan dinilai
        $att1->refresh();
        $att2->refresh();

        $this->assertNotNull($att1->completed_at);
        $this->assertTrue((bool)$att1->is_force_submitted);
        $this->assertEquals(100.0, (float)$att1->score);

        $this->assertNotNull($att2->completed_at);
        $this->assertTrue((bool)$att2->is_force_submitted);
        $this->assertEquals(0.0, (float)$att2->score);

        // Mahasiswa yang sedang di arena play memantau status sesi via session-check otomatis menerima is_ended = true
        $sessionCheckRes = $this->withSession(['quiz_attempt_' . $quiz->id => $att1->id])
            ->getJson(route('quiz.session.check', ['token' => $quiz->unique_token, 'attempt' => $att1->id]));
        $sessionCheckRes->assertStatus(200);
        $sessionCheckRes->assertJson([
            'status'   => 'ended',
            'is_ended' => true,
            'redirect' => route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $att1->id]),
        ]);

        // Mahasiswa yang melakukan autosave otomatis mendapat signal redirect ke results
        $autosaveRes = $this->withSession(['quiz_attempt_' . $quiz->id => $att1->id])
            ->postJson(route('quiz.autosave', ['token' => $quiz->unique_token, 'attempt' => $att1->id]), [
                'answers' => [],
            ]);

        $autosaveRes->assertStatus(200);
        $autosaveRes->assertJson([
            'is_ended'     => true,
            'is_completed' => true,
            'redirect'     => route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $att1->id]),
        ]);

        // Admin dapat mereset sesi kembali ke waiting jika ingin membuka gelombang baru
        $resetRes = $this->actingAs($this->admin)->postJson(route('admin.quizzes.session.reset', $quiz));
        $resetRes->assertStatus(200);
        $resetRes->assertJson(['success' => true, 'session_status' => 'waiting']);

        $quiz->refresh();
        $this->assertTrue($quiz->isSessionWaiting());
    }

    public function test_countdown_timer_does_not_run_while_in_waiting_room(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Praktikum Algoritma',
            'session_status'     => 'waiting',
            'time_limit_minutes' => 20,
            'is_active'          => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '13020210088',
            'student_name'          => 'Andi Pratama',
            'total_points_possible' => 100,
            'started_at'            => now()->subMinutes(15), // Gabung ruang tunggu 15 menit lalu
        ]);

        // 1. timeRemainingSeconds harus tetap utuh penuh (20 * 60 = 1200 detik), TIDAK berkurang karena kuis belum di-start
        $this->assertEquals(20 * 60, $attempt->timeRemainingSeconds($quiz));

        // 2. Di live monitor admin, sisa waktu berstatus siaga dan waktu mulai bertuliskan 'Menunggu Mulai'
        $monitorRes = $this->actingAs($this->admin)->getJson(route('admin.quizzes.monitor.data', $quiz));
        $monitorRes->assertStatus(200);
        $data = $monitorRes->json();
        $this->assertEquals(1200, $data['active'][0]['time_remaining_seconds']);
        $this->assertStringContainsString('(Siaga)', $data['active'][0]['time_remaining_human']);
        $this->assertEquals('Menunggu Mulai', $data['active'][0]['started_at_human']);

        // 3. Setelah admin menekan start session, baru timer mulai berjalan
        $this->actingAs($this->admin)->postJson(route('admin.quizzes.session.start', $quiz));
        $attempt->refresh();
        $quiz->refresh();

        $monitorAfterStart = $this->actingAs($this->admin)->getJson(route('admin.quizzes.monitor.data', $quiz));
        $monitorAfterStart->assertStatus(200);
        $dataAfter = $monitorAfterStart->json();
        $this->assertStringNotContainsString('(Siaga)', $dataAfter['active'][0]['time_remaining_human']);
        $this->assertNotEquals('Menunggu Mulai', $dataAfter['active'][0]['started_at_human']);
    }
}

