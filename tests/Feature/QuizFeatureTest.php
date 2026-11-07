<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizFeatureTest extends TestCase
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

    public function test_admin_can_view_quizzes_index_and_create_quiz(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.quizzes.index'));
        $response->assertStatus(200);
        $response->assertSee('Kuis Interaktif');

        // Create quiz
        $quizData = [
            'title'              => 'Kuis Praktikum Algoritma',
            'description'        => 'Uji pemahaman dasar variabel dan loop',
            'time_limit_minutes' => 15,
            'time_per_question'  => 30,
            'shuffle_questions'  => '1',
            'shuffle_options'    => '1',
            'show_leaderboard'   => '1',
            'show_review'        => '1',
            'max_attempts'       => 2,
            'is_active'          => '1',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), $quizData);
        $postResponse->assertSessionHas('success');

        $quiz = Quiz::where('title', 'Kuis Praktikum Algoritma')->first();
        $this->assertNotNull($quiz);
        $this->assertNotEmpty($quiz->access_code);
        $this->assertNotEmpty($quiz->unique_token);
        $this->assertTrue($quiz->is_active);

        $postResponse->assertRedirect(route('admin.quizzes.questions', $quiz));
    }

    public function test_admin_can_add_questions_with_options_to_quiz(): void
    {
        $quiz = Quiz::create([
            'user_id' => $this->admin->id,
            'title'   => 'Kuis Jaringan',
        ]);

        $questionData = [
            'question_text' => 'Protokol apa yang berjalan di port 80?',
            'question_type' => 'multiple_choice',
            'points'        => 20,
            'explanation'   => 'Port 80 adalah standar port HTTP tanpa enkripsi SSL.',
            'options'       => ['HTTP', 'HTTPS', 'SSH', 'FTP'],
            'correct_index' => 0, // 'HTTP' is correct
        ];

        $res = $this->actingAs($this->admin)->post(route('admin.quizzes.questions.store', $quiz), $questionData);
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id'       => $quiz->id,
            'question_text' => 'Protokol apa yang berjalan di port 80?',
            'points'        => 20,
        ]);

        $question = $quiz->questions()->first();
        $this->assertCount(4, $question->options);
        $correctOpt = $question->options()->where('is_correct', true)->first();
        $this->assertEquals('HTTP', $correctOpt->option_text);
    }

    public function test_admin_can_update_and_delete_question(): void
    {
        $quiz = Quiz::create([
            'user_id' => $this->admin->id,
            'title'   => 'Kuis Basis Data',
        ]);

        $q = $quiz->questions()->create([
            'question_text' => 'SQL singkatan dari?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
        ]);
        $q->options()->create(['option_text' => 'Structured Query Language', 'is_correct' => true, 'order_num' => 1]);
        $q->options()->create(['option_text' => 'Simple Query Language', 'is_correct' => false, 'order_num' => 2]);

        // Update question
        $updateData = [
            'question_text' => 'SQL adalah singkatan dari apa?',
            'question_type' => 'multiple_choice',
            'points'        => 15,
            'explanation'   => 'Structured Query Language dipakai untuk RDBMS.',
            'options'       => ['Structured Query Language', 'Simple Question Link'],
            'correct_index' => 0,
        ];

        $updateRes = $this->actingAs($this->admin)->put(route('admin.quizzes.questions.update', [$quiz, $q]), $updateData);
        $updateRes->assertSessionHas('success');

        $q->refresh();
        $this->assertEquals('SQL adalah singkatan dari apa?', $q->question_text);
        $this->assertEquals(15, $q->points);

        // Delete question
        $deleteRes = $this->actingAs($this->admin)->delete(route('admin.quizzes.questions.destroy', [$quiz, $q]));
        $deleteRes->assertSessionHas('success');
        $this->assertDatabaseMissing('quiz_questions', ['id' => $q->id]);
    }

    public function test_admin_can_toggle_quiz_active_status(): void
    {
        $quiz = Quiz::create([
            'user_id'   => $this->admin->id,
            'title'     => 'Kuis Toggle Test',
            'is_active' => true,
        ]);

        $res = $this->actingAs($this->admin)->patch(route('admin.quizzes.toggle', $quiz));
        $res->assertSessionHas('success');

        $quiz->refresh();
        $this->assertFalse($quiz->is_active);
    }

    public function test_student_can_join_quiz_via_access_code(): void
    {
        $quiz = Quiz::create([
            'user_id'     => $this->admin->id,
            'title'       => 'Kuis Join Kode',
            'access_code' => 'LAB-TEST1',
            'is_active'   => true,
        ]);

        $res = $this->post(route('quiz.join'), ['access_code' => 'LAB-TEST1']);
        $res->assertRedirect(route('quiz.lobby', ['token' => $quiz->unique_token]));

        // Invalid code
        $failRes = $this->post(route('quiz.join'), ['access_code' => 'INVALID-CODE']);
        $failRes->assertSessionHas('quiz_error');
    }

    public function test_student_can_start_attempt_and_navigate_play_arena(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Arena Mahasiswa',
            'time_limit_minutes' => 10,
            'is_active'          => true,
            'session_status'     => 'started',
            'max_attempts'       => 1,
        ]);

        $q = $quiz->questions()->create([
            'question_text' => 'Berapa 5 + 5?',
            'question_type' => 'multiple_choice',
            'points'        => 50,
        ]);
        $opt1 = $q->options()->create(['option_text' => '10', 'is_correct' => true, 'order_num' => 1]);
        $opt2 = $q->options()->create(['option_text' => '15', 'is_correct' => false, 'order_num' => 2]);

        // Start attempt
        $startRes = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '220101099',
            'nama' => 'Rina Wijaya',
        ]);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('nim', '220101099')->first();
        $this->assertNotNull($attempt);
        $this->assertEquals(50, $attempt->total_points_possible);

        $startRes->assertRedirect(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        // Access play page
        $playRes = $this->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $playRes->assertStatus(200);
        $playRes->assertSee('Rina Wijaya');
        $playRes->assertSee('Berapa 5 + 5?');
        // Ensure client payload does NOT leak is_correct:
        $this->assertStringNotContainsString('"is_correct"', $playRes->getContent());

        // Test anti-cheat tab switch tracking
        $tabRes = $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $tabRes->assertJson(['success' => true, 'tab_switch_count' => 1, 'strike' => 1, 'penalty_seconds' => 60]);
        $attempt->refresh();
        $this->assertEquals(1, $attempt->tab_switch_count);
        $this->assertNotNull($attempt->penalty_end_time);
    }

    public function test_anti_cheat_three_strikes_and_unlock_mechanisms(): void
    {
        $quiz = Quiz::create([
            'user_id'                => $this->admin->id,
            'title'                  => 'Ujian Integritas Algoritma',
            'is_active'              => true,
            'allow_open_book'        => false,
            'admin_unlock_password'  => 'LABRAHASIA2026',
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101999',
            'student_name'          => 'Peserta Ujian',
            'total_points_possible' => 100,
            'started_at'            => now(),
            'tab_switch_count'      => 0,
        ]);

        // Strike 1: 1 menit (60s) penalty
        $res1 = $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $res1->assertJson([
            'success'          => true,
            'tab_switch_count' => 1,
            'strike'           => 1,
            'penalty_seconds'  => 60,
            'is_locked'        => false,
        ]);

        // Strike 2: 1.5 menit (90s) penalty
        $res2 = $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $res2->assertJson([
            'success'          => true,
            'tab_switch_count' => 2,
            'strike'           => 2,
            'penalty_seconds'  => 90,
            'is_locked'        => false,
        ]);

        // Strike 3: Total lockout
        $res3 = $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $res3->assertJson([
            'success'          => true,
            'tab_switch_count' => 3,
            'strike'           => 3,
            'is_locked'        => true,
        ]);

        $attempt->refresh();
        $this->assertTrue($attempt->is_locked);

        // Attempt unlock with wrong password
        $failUnlock = $this->postJson(route('quiz.unlock-password', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]), [
            'admin_password' => 'SALAH_PASSWORD',
        ]);
        $failUnlock->assertStatus(422);
        $attempt->refresh();
        $this->assertTrue($attempt->is_locked);

        // Attempt unlock with correct custom password
        $successUnlock = $this->postJson(route('quiz.unlock-password', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]), [
            'admin_password' => 'LABRAHASIA2026',
        ]);
        $successUnlock->assertStatus(200);
        $successUnlock->assertJson(['success' => true]);

        $attempt->refresh();
        $this->assertFalse($attempt->is_locked);
        $this->assertNull($attempt->penalty_end_time);

        // Now test locking again and admin remote unlock via dashboard
        $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $attempt->refresh();
        $this->assertTrue($attempt->is_locked);

        $remoteUnlock = $this->actingAs($this->admin)->post(route('admin.quizzes.attempts.unlock', ['quiz' => $quiz->id, 'attempt' => $attempt->id]));
        $remoteUnlock->assertSessionHas('success');
        $attempt->refresh();
        $this->assertFalse($attempt->is_locked);
    }

    public function test_open_book_mode_does_not_penalize_tab_switch(): void
    {
        $quiz = Quiz::create([
            'user_id'         => $this->admin->id,
            'title'           => 'Praktikum Open Book Modul',
            'is_active'       => true,
            'allow_open_book' => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101888',
            'student_name'          => 'Mahasiswa Santai',
            'total_points_possible' => 100,
            'started_at'            => now(),
        ]);

        $res = $this->post(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $res->assertJson([
            'success'         => true,
            'allow_open_book' => true,
            'penalty_seconds' => 0,
            'is_locked'       => false,
        ]);

        $attempt->refresh();
        $this->assertEquals(0, $attempt->tab_switch_count);
        $this->assertFalse($attempt->is_locked);
    }

    public function test_student_can_submit_answers_and_auto_grading_is_exact(): void
    {
        $quiz = Quiz::create([
            'user_id'          => $this->admin->id,
            'title'            => 'Kuis Evaluasi Algoritma',
            'is_active'        => true,
            'show_leaderboard' => true,
            'show_review'      => true,
        ]);

        // Question 1: 50 points
        $q1 = $quiz->questions()->create(['question_text' => 'Q1', 'question_type' => 'multiple_choice', 'points' => 50]);
        $q1Correct = $q1->options()->create(['option_text' => 'Benar1', 'is_correct' => true, 'order_num' => 1]);
        $q1Wrong = $q1->options()->create(['option_text' => 'Salah1', 'is_correct' => false, 'order_num' => 2]);

        // Question 2: 50 points
        $q2 = $quiz->questions()->create(['question_text' => 'Q2', 'question_type' => 'multiple_choice', 'points' => 50]);
        $q2Correct = $q2->options()->create(['option_text' => 'Benar2', 'is_correct' => true, 'order_num' => 1]);
        $q2Wrong = $q2->options()->create(['option_text' => 'Salah2', 'is_correct' => false, 'order_num' => 2]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101001',
            'student_name'          => 'Mahasiswa Juara',
            'total_points_possible' => 100,
            'started_at'            => now()->subMinutes(3),
        ]);

        // Student answers Q1 correctly, Q2 wrongly
        $submitPayload = [
            'answers' => [
                $q1->id => $q1Correct->id,
                $q2->id => $q2Wrong->id,
            ],
        ];

        $res = $this->post(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]), $submitPayload);
        $res->assertRedirect(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $attempt->refresh();
        $this->assertNotNull($attempt->completed_at);
        $this->assertEquals(1, $attempt->correct_answers_count);
        $this->assertEquals(1, $attempt->wrong_answers_count);
        $this->assertEquals(50, $attempt->total_points_earned);
        $this->assertEquals(50.0, (float)$attempt->score);

        // View Results Page
        $resultsRes = $this->get(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $resultsRes->assertStatus(200);
        $resultsRes->assertSee('50.0');
        $resultsRes->assertSee('Mahasiswa Juara');
    }

    public function test_admin_can_export_csv_and_reset_attempts(): void
    {
        $quiz = Quiz::create([
            'user_id'   => $this->admin->id,
            'title'     => 'Kuis Export Test',
            'is_active' => true,
        ]);

        QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101077',
            'student_name'          => 'Budi Santoso',
            'score'                 => 90.0,
            'total_points_earned'   => 90,
            'total_points_possible' => 100,
            'correct_answers_count' => 9,
            'wrong_answers_count'   => 1,
            'started_at'            => now()->subMinutes(5),
            'completed_at'          => now(),
            'duration_seconds'      => 300,
            'tab_switch_count'      => 0,
        ]);

        // Export CSV
        $csvRes = $this->actingAs($this->admin)->get(route('admin.quizzes.export-csv', $quiz));
        $csvRes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvRes->headers->get('Content-Type'));

        // Reset all attempts
        $resetRes = $this->actingAs($this->admin)->delete(route('admin.quizzes.reset-attempts', $quiz));
        $resetRes->assertSessionHas('success');
        $this->assertEquals(0, $quiz->attempts()->count());
    }

    public function test_autosave_answers_and_fullscreen_violation_reason(): void
    {
        $quiz = Quiz::create([
            'user_id'        => $this->admin->id,
            'title'          => 'Kuis Autosave Test',
            'is_active'      => true,
            'session_status' => 'started',
        ]);

        $q1 = $quiz->questions()->create(['question_text' => 'Q1', 'question_type' => 'multiple_choice', 'points' => 100]);
        $opt1 = $q1->options()->create(['option_text' => 'A1', 'is_correct' => true, 'order_num' => 1]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101555',
            'student_name'          => 'Autosave Tester',
            'total_points_possible' => 100,
            'started_at'            => now(),
        ]);

        // 1. Test autosave endpoint
        $autosaveRes = $this->postJson(route('quiz.autosave', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]), [
            'answers' => [
                $q1->id => $opt1->id,
            ],
        ]);
        $autosaveRes->assertStatus(200);
        $autosaveRes->assertJson(['success' => true]);

        $attempt->refresh();
        $this->assertEquals([$q1->id => $opt1->id], $attempt->draft_answers);

        // 2. Test play arena loads saved answers
        $playRes = $this->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));
        $playRes->assertStatus(200);
        $playRes->assertViewHas('savedAnswers', [$q1->id => $opt1->id]);

        // 3. Test submitting with empty answers uses draft_answers fallback
        $submitRes = $this->postJson(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]), []);
        $submitRes->assertJson(['success' => true]);

        $attempt->refresh();
        $this->assertNotNull($attempt->completed_at);
        $this->assertEquals(100.0, (float)$attempt->score);
        $this->assertEquals(1, $attempt->correct_answers_count);

        // 4. Test fullscreen_exit violation reason
        $attempt2 = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '220101556',
            'student_name'          => 'Kiosk Tester',
            'total_points_possible' => 100,
            'started_at'            => now(),
        ]);

        $exitRes = $this->postJson(route('quiz.tab-switch', ['token' => $quiz->unique_token, 'attempt' => $attempt2->id]), [
            'reason' => 'fullscreen_exit',
        ]);
        $exitRes->assertJson([
            'success'          => true,
            'strike'           => 1,
            'penalty_seconds'  => 60,
        ]);
        $attempt2->refresh();
        $this->assertEquals('fullscreen_exit', $attempt2->violations_log[0]['type']);
    }

    public function test_idor_session_protection_and_expired_draft_fallback(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'Kuis Uji IDOR & Keamanan',
            'time_limit_minutes' => 5,
            'is_active'          => true,
            'session_status'     => 'started',
            'max_attempts'       => 1,
        ]);

        $q = $quiz->questions()->create([
            'question_text' => 'Soal Keamanan',
            'question_type' => 'multiple_choice',
            'points'        => 100,
        ]);
        $opt = $q->options()->create(['option_text' => 'Benar', 'is_correct' => true, 'order_num' => 1]);

        // Student 1 starts quiz
        $resStart1 = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '11111111',
            'nama' => 'Student One',
        ]);
        $attempt1 = QuizAttempt::where('nim', '11111111')->first();
        $this->assertNotNull($attempt1);

        // Student 2 starts quiz in separate session
        $attempt2 = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '22222222',
            'student_name'          => 'Student Two',
            'total_points_possible' => 100,
            'started_at'            => now(),
        ]);

        // Student 1 tries to access Student 2 attempt
        $idorRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt1->id])
            ->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt2->id]));
        $idorRes->assertStatus(403);

        // Student 1 tries to submit Student 2 attempt
        $idorSubmit = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt1->id])
            ->post(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt2->id]), []);
        $idorSubmit->assertStatus(403);

        // Test expired time draft recovery:
        $attempt1->update([
            'started_at'    => now()->subMinutes(10),
            'draft_answers' => [$q->id => $opt->id],
        ]);

        // Accessing play should auto-finalize with draft answers!
        $expiredRes = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt1->id])
            ->get(route('quiz.play', ['token' => $quiz->unique_token, 'attempt' => $attempt1->id]));
        $expiredRes->assertRedirect(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt1->id]));

        $attempt1->refresh();
        $this->assertNotNull($attempt1->completed_at);
        $this->assertEquals(100.0, (float)$attempt1->score);
        $this->assertEquals(1, $attempt1->correct_answers_count);
    }

    public function test_quiz_schedule_and_access_password_enforcement(): void
    {
        // 1. Test Upcoming Quiz (starts in 1 hour)
        $upcomingQuiz = Quiz::create([
            'user_id'          => $this->admin->id,
            'title'            => 'Kuis Belum Mulai',
            'starts_at'        => now()->addHour(),
            'access_password'  => 'kunci123',
        ]);

        $lobbyRes = $this->get(route('quiz.lobby', $upcomingQuiz->unique_token));
        $lobbyRes->assertStatus(200);
        $lobbyRes->assertSee('Kuis Belum Dimulai');

        $startUpcoming = $this->post(route('quiz.start', $upcomingQuiz->unique_token), [
            'nim'             => '12345678',
            'nama'            => 'Budi Test',
            'access_password' => 'kunci123',
        ]);
        $startUpcoming->assertSessionHas('error');
        $this->assertStringContainsString('belum dimulai', session('error'));

        // 2. Test Expired Quiz (ended 1 hour ago)
        $expiredQuiz = Quiz::create([
            'user_id'         => $this->admin->id,
            'title'           => 'Kuis Sudah Tutup',
            'ends_at'         => now()->subHour(),
        ]);

        $lobbyExpired = $this->get(route('quiz.lobby', $expiredQuiz->unique_token));
        $lobbyExpired->assertStatus(200);
        $lobbyExpired->assertSee('Batas Waktu Akses Telah Berakhir');

        $startExpired = $this->post(route('quiz.start', $expiredQuiz->unique_token), [
            'nim'  => '12345678',
            'nama' => 'Budi Test',
        ]);
        $startExpired->assertSessionHas('error');
        $this->assertStringContainsString('telah berakhir', session('error'));

        // 3. Test Access Password enforcement on active quiz
        $pwQuiz = Quiz::create([
            'user_id'         => $this->admin->id,
            'title'           => 'Kuis Dengan Password',
            'access_password' => 'secretPass99',
        ]);

        // Wrong password fails
        $startWrongPw = $this->post(route('quiz.start', $pwQuiz->unique_token), [
            'nim'             => '12345678',
            'nama'            => 'Budi Test',
            'access_password' => 'wrong',
        ]);
        $startWrongPw->assertSessionHas('error');
        $this->assertStringContainsString('Password akses kuis salah', session('error'));

        // Correct password succeeds
        $startCorrectPw = $this->post(route('quiz.start', $pwQuiz->unique_token), [
            'nim'             => '12345678',
            'nama'            => 'Budi Test',
            'access_password' => 'secretPass99',
        ]);
        $startCorrectPw->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $pwQuiz->id,
            'nim'     => '12345678',
        ]);
    }

    public function test_multi_select_question_grading_and_image_upload(): void
    {
        Storage::fake('public');

        $quiz = Quiz::create([
            'user_id' => $this->admin->id,
            'title'   => 'Kuis Pilihan Jamak & Gambar',
        ]);

        // 1. Admin creates multi-select question with question image & option images
        $qImage = UploadedFile::fake()->image('soal_arsitektur.png', 800, 600);
        $opt0Image = UploadedFile::fake()->image('opt_ram.jpg', 400, 300);

        $createResponse = $this->actingAs($this->admin)->post(route('admin.quizzes.questions.store', $quiz), [
            'question_text'   => 'Pilih komponen komputer yang merupakan memori volatil:',
            'points'          => 100,
            'options'         => ['RAM', 'Hard Disk', 'Cache L1', 'Flash Disk'],
            'correct_indices' => [0, 2], // RAM and Cache L1
            'question_image'  => $qImage,
            'option_images'   => [
                0 => $opt0Image,
            ],
        ]);

        $createResponse->assertSessionHas('success');

        $question = $quiz->questions()->first();
        $this->assertNotNull($question);
        $this->assertEquals('multi_select', $question->question_type);
        $this->assertNotNull($question->image_path);
        Storage::disk('public')->assertExists($question->image_path);

        $options = $question->options()->orderBy('order_num')->get();
        $this->assertCount(4, $options);
        $this->assertTrue($options[0]->is_correct); // RAM
        $this->assertFalse($options[1]->is_correct); // Hard Disk
        $this->assertTrue($options[2]->is_correct); // Cache L1
        $this->assertFalse($options[3]->is_correct); // Flash Disk
        $this->assertNotNull($options[0]->image_path);
        Storage::disk('public')->assertExists($options[0]->image_path);
        $this->assertStringStartsWith('/storage/', $options[0]->image_url);
        $storageResponse = $this->get($options[0]->image_url);
        $storageResponse->assertStatus(200);

        // 2. Student Submits Perfect Answer (both RAM and Cache L1)
        $start1 = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '99990001',
            'nama' => 'Siswa Pintar',
        ]);
        $attempt1 = QuizAttempt::where('nim', '99990001')->first();

        $submit1 = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt1->id])
            ->post(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt1->id]), [
                'answers' => [
                    $question->id => [$options[0]->id, $options[2]->id],
                ],
            ]);
        $submit1->assertRedirect();
        $attempt1->refresh();
        $this->assertEquals(100.0, (float)$attempt1->score);
        $this->assertEquals(1, $attempt1->correct_answers_count);

        // 3. Student Submits Partial Answer (only RAM, no wrong options)
        $start2 = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '99990002',
            'nama' => 'Siswa Parsial',
        ]);
        $attempt2 = QuizAttempt::where('nim', '99990002')->first();

        $submit2 = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt2->id])
            ->post(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt2->id]), [
                'answers' => [
                    $question->id => [$options[0]->id],
                ],
            ]);
        $submit2->assertRedirect();
        $attempt2->refresh();
        $this->assertEquals(50.0, (float)$attempt2->score); // 1 out of 2 correct = 50%
        $this->assertEquals(0, $attempt2->correct_answers_count);

        // 4. Student Submits Mixed Answer (1 correct, 1 wrong: RAM and Hard Disk)
        $start3 = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '99990003',
            'nama' => 'Siswa Salah',
        ]);
        $attempt3 = QuizAttempt::where('nim', '99990003')->first();

        $submit3 = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt3->id])
            ->post(route('quiz.submit', ['token' => $quiz->unique_token, 'attempt' => $attempt3->id]), [
                'answers' => [
                    $question->id => [$options[0]->id, $options[1]->id],
                ],
            ]);
        $submit3->assertRedirect();
        $attempt3->refresh();
        $this->assertEquals(0.0, (float)$attempt3->score); // penalty: wrong answer selected = 0 points
        $this->assertEquals(0, $attempt3->correct_answers_count);
    }

    public function test_custom_quiz_access_code_creation_update_and_join(): void
    {
        // 1. Admin creates quiz with custom access code 'algo-modul-1'
        $postRes = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'       => 'Kuis Algoritma Pemrograman',
            'access_code' => 'algo-modul-1',
        ]);
        $postRes->assertSessionHas('success');

        $quiz = Quiz::where('title', 'Kuis Algoritma Pemrograman')->first();
        $this->assertNotNull($quiz);
        $this->assertEquals('ALGO-MODUL-1', $quiz->access_code);

        // Student can join using the custom access code (case-insensitive)
        $joinRes = $this->post(route('quiz.join'), ['access_code' => 'algo-modul-1']);
        $joinRes->assertRedirect(route('quiz.lobby', $quiz->unique_token));

        // 2. Admin updates the access code to 'STRUKDAT-99'
        $updateRes = $this->actingAs($this->admin)->put(route('admin.quizzes.update', $quiz), [
            'title'       => 'Kuis Algoritma Pemrograman (Updated)',
            'access_code' => 'STRUKDAT-99',
            'is_active'   => '1',
        ]);
        $updateRes->assertSessionHas('success');

        $quiz->refresh();
        $this->assertEquals('STRUKDAT-99', $quiz->access_code);

        $joinNewRes = $this->post(route('quiz.join'), ['access_code' => 'strukdat-99']);
        $joinNewRes->assertRedirect(route('quiz.lobby', $quiz->unique_token));

        // 3. Duplicate access code is rejected
        $dupRes = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'       => 'Kuis Lain Duplikat',
            'access_code' => 'STRUKDAT-99',
        ]);
        $dupRes->assertSessionHasErrors('access_code');

        // 4. Invalid characters with spaces or symbols are rejected
        $invalidRes = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'       => 'Kuis Invalid Code',
            'access_code' => 'INVALID CODE!',
        ]);
        $invalidRes->assertSessionHasErrors('access_code');
    }

    public function test_quiz_create_and_edit_routes_redirect_to_index_with_modal_triggers(): void
    {
        // 1. GET /admin/quizzes/create should redirect to index with open_create_modal
        $createRes = $this->actingAs($this->admin)->get(route('admin.quizzes.create'));
        $createRes->assertRedirect(route('admin.quizzes.index'));
        $createRes->assertSessionHas('open_create_modal', true);

        // 2. Create sample quiz
        $quiz = Quiz::create([
            'user_id'     => $this->admin->id,
            'title'       => 'Kuis Jaringan Komputer',
            'access_code' => 'NET-101',
            'is_active'   => true,
        ]);

        // 3. GET /admin/quizzes/{quiz}/edit should redirect to index with edit_quiz_id
        $editRes = $this->actingAs($this->admin)->get(route('admin.quizzes.edit', $quiz));
        $editRes->assertRedirect(route('admin.quizzes.index'));
        $editRes->assertSessionHas('edit_quiz_id', $quiz->id);

        // 4. Index view contains create and edit modal popups
        $indexRes = $this->actingAs($this->admin)->get(route('admin.quizzes.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('id="createQuizModal"', false);
        $indexRes->assertSee('id="editQuizModal"', false);
        $indexRes->assertSee('openCreateQuizModal()', false);
        $indexRes->assertSee('openEditQuizModal(' . $quiz->id . ')', false);
    }

    public function test_quiz_results_page_renders_hero_score_card_and_student_answer_review_modal(): void
    {
        $quiz = Quiz::create([
            'user_id'     => $this->admin->id,
            'title'       => 'Pre-Test Modul 2: Algoritma & Pointer',
            'access_code' => 'ALGO-PTR',
            'is_active'   => true,
        ]);

        $question = $quiz->questions()->create([
            'question_text' => 'Berapakah ukuran pointer pada arsitektur 64-bit?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
            'explanation'   => 'Arsitektur 64-bit memiliki alamat memori sepanjang 8 bytes.',
        ]);

        $opt1 = $question->options()->create(['option_text' => '8 bytes', 'is_correct' => true, 'order_num' => 1]);
        $opt2 = $question->options()->create(['option_text' => '4 bytes', 'is_correct' => false, 'order_num' => 2]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '22010101',
            'student_name'          => 'Budi Santoso',
            'score'                 => 100.0,
            'total_points_earned'   => 10,
            'total_points_possible' => 10,
            'correct_answers_count' => 1,
            'wrong_answers_count'   => 0,
            'duration_seconds'      => 123,
            'tab_switch_count'      => 0,
            'completed_at'          => now(),
        ]);

        $attempt->answers()->create([
            'quiz_question_id'   => $question->id,
            'selected_option_id' => $opt1->id,
            'is_correct'         => true,
            'points_earned'      => 10,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quizzes.results', $quiz));
        $response->assertStatus(200);

        // Verify Hero Card & Answer Sheet elements are present in the view
        $response->assertSee('Pengerjaan Kuis Selesai');
        $response->assertSee('Skor Akhir');
        $response->assertSee('Jawaban Benar');
        $response->assertSee('Jawaban Salah');
        $response->assertSee('Waktu Selesai');
        $response->assertSee('Pengawas Anti-Cheat');
        $response->assertSee('Lembar Jawaban', false);
        $response->assertSee('viewAttemptDetail', false);
        $response->assertSee('Cetak PDF', false);
        $response->assertSee('@media print', false);
    }

    public function test_admin_dashboard_renders_cbt_statistics_and_recent_quiz_attempts(): void
    {
        $quiz = Quiz::create([
            'user_id'            => $this->admin->id,
            'title'              => 'CBT Dashboard Test',
            'time_limit_minutes' => 20,
            'is_active'          => true,
            'unique_token'       => 'cbt-token-123',
        ]);

        QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '22019999',
            'student_name'          => 'Citra Kirana',
            'kelas'                 => 'IF-01',
            'score'                 => 95.0,
            'total_points_earned'   => 19,
            'total_points_possible' => 20,
            'correct_answers_count' => 19,
            'wrong_answers_count'   => 1,
            'duration_seconds'      => 450,
            'tab_switch_count'      => 0,
            'completed_at'          => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Verify CBT Cards & Recent Attempts
        $response->assertSee('Kuis Interaktif (CBT Exam)');
        $response->assertSee('Bank Soal Ujian');
        $response->assertSee('Sesi Ujian CBT Terbaru');
        $response->assertSee('Citra Kirana');
        $response->assertSee('22019999');
        $response->assertSee('CBT Dashboard Test');
    }

    public function test_admin_can_reorder_quiz_questions_and_updates_order_nums(): void
    {
        $quiz = Quiz::create([
            'title'       => 'Kuis Urutan Soal',
            'user_id'     => $this->admin->id,
            'is_active'   => true,
            'access_code' => 'REORDER-01',
        ]);

        $q1 = $quiz->questions()->create(['question_text' => 'Soal Pertama', 'points' => 10, 'order_num' => 1]);
        $q2 = $quiz->questions()->create(['question_text' => 'Soal Kedua', 'points' => 10, 'order_num' => 2]);
        $q3 = $quiz->questions()->create(['question_text' => 'Soal Ketiga', 'points' => 10, 'order_num' => 3]);
        $q4 = $quiz->questions()->create(['question_text' => 'Soal Keempat', 'points' => 10, 'order_num' => 4]);

        // Check index view renders 2-column and dropdown order
        $viewRes = $this->actingAs($this->admin)->get(route('admin.quizzes.questions', $quiz));
        $viewRes->assertOk();
        $viewRes->assertSee('grid-cols-1 lg:grid-cols-2', false);
        $viewRes->assertDontSee('Soal Ganjil');
        $viewRes->assertDontSee('Soal Genap');
        $viewRes->assertSee('changeQuestionOrder', false);

        // Move Q4 to position 2
        $reorderRes = $this->actingAs($this->admin)->postJson(route('admin.quizzes.questions.reorder', [$quiz, $q4]), [
            'order' => 2,
        ]);

        $reorderRes->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        // Reload questions
        $ordered = $quiz->questions()->get()->pluck('id')->all();
        $this->assertEquals([$q1->id, $q4->id, $q2->id, $q3->id], $ordered);

        // Check sequential order_num
        $orderNums = $quiz->questions()->get()->pluck('order_num')->all();
        $this->assertEquals([1, 2, 3, 4], $orderNums);

        // Now move Q1 (pos 1) to pos 4
        $reorderRes2 = $this->actingAs($this->admin)->postJson(route('admin.quizzes.questions.reorder', [$quiz, $q1]), [
            'order' => 4,
        ]);
        $reorderRes2->assertOk();

        $ordered2 = $quiz->questions()->get()->pluck('id')->all();
        $this->assertEquals([$q4->id, $q2->id, $q3->id, $q1->id], $ordered2);
        $this->assertEquals([1, 2, 3, 4], $quiz->questions()->get()->pluck('order_num')->all());
    }

    public function test_admin_reordering_shifts_numbers_automatically_for_q2_to_q1_and_last_to_first(): void
    {
        $quiz = Quiz::create([
            'title'       => 'Kuis Pergeseran Urutan Soal',
            'user_id'     => $this->admin->id,
            'is_active'   => true,
            'access_code' => 'SHIFT-01',
        ]);

        $createdQuestions = [];
        for ($i = 1; $i <= 10; $i++) {
            $createdQuestions[$i] = $quiz->questions()->create([
                'question_text' => "Soal #{$i}",
                'points'        => 10,
                'order_num'     => $i,
            ]);
        }

        // Scenario 1: Ubah soal ke-2 menjadi nomor 1 -> soal 1 bergeser ke 2
        $res1 = $this->actingAs($this->admin)->postJson(route('admin.quizzes.questions.reorder', [$quiz, $createdQuestions[2]]), [
            'order' => 1,
        ]);
        $res1->assertOk()->assertJson(['success' => true]);

        $orderAfterScenario1 = $quiz->questions()->get();
        // Index 0 harus Soal #2 dengan order_num = 1
        $this->assertEquals($createdQuestions[2]->id, $orderAfterScenario1[0]->id);
        $this->assertEquals(1, $orderAfterScenario1[0]->order_num);
        // Index 1 harus Soal #1 dengan order_num = 2 (otomatis bergeser!)
        $this->assertEquals($createdQuestions[1]->id, $orderAfterScenario1[1]->id);
        $this->assertEquals(2, $orderAfterScenario1[1]->order_num);
        // Seluruh order_num harus 1 s/d 10 urut
        $this->assertEquals(range(1, 10), $orderAfterScenario1->pluck('order_num')->all());

        // Scenario 2: Ubah soal paling terakhir (soal ke-10) menjadi nomor 1 -> seluruh soal 1-9 bergeser turun
        $lastQuestion = $orderAfterScenario1->last();
        $res2 = $this->actingAs($this->admin)->postJson(route('admin.quizzes.questions.reorder', [$quiz, $lastQuestion]), [
            'order' => 1,
        ]);
        $res2->assertOk()->assertJson(['success' => true]);

        $orderAfterScenario2 = $quiz->questions()->get();
        // Soal terakhir sekarang ada di urutan pertama (#1)
        $this->assertEquals($lastQuestion->id, $orderAfterScenario2->first()->id);
        $this->assertEquals(1, $orderAfterScenario2->first()->order_num);
        // Seluruh soal lainnya bergeser urut 1 s/d 10
        $this->assertEquals(range(1, 10), $orderAfterScenario2->pluck('order_num')->all());
        $this->assertCount(10, $orderAfterScenario2);
    }
}
