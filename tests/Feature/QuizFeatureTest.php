<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $tabRes->assertJson(['success' => true, 'tab_switch_count' => 1]);
        $attempt->refresh();
        $this->assertEquals(1, $attempt->tab_switch_count);
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
}
