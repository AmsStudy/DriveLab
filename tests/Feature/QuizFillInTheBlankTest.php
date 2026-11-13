<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFillInTheBlankTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $this->quiz = Quiz::create([
            'user_id'             => $this->admin->id,
            'title'               => 'Kuis Isian Rumpang Test',
            'description'         => 'Testing fill in the blank questions',
            'unique_token'        => 'fill-blank-token-123',
            'access_code'         => 'FB1234',
            'time_limit_minutes'  => 30,
            'shuffle_questions'   => false,
            'shuffle_options'     => false,
            'is_active'           => true,
            'is_session_active'   => true,
            'session_status'      => 'active',
        ]);
    }

    public function test_admin_can_create_fill_in_the_blank_question(): void
    {
        $payload = [
            'question_type' => 'fill_blank',
            'points'        => 20,
            'question_text' => 'Apa output dari kode berikut adalah [20] dan [10|sepuluh]?',
            'options'       => [
                '20',
                '10|sepuluh',
            ],
            'explanation'   => 'Outer.this.x bernilai 10 dan inner bernilai 20',
        ];

        $response = $this->actingAs($this->admin)->post(
            route('admin.quizzes.questions.store', $this->quiz),
            $payload
        );

        $response->assertSessionHas('success');

        $question = QuizQuestion::where('quiz_id', $this->quiz->id)->first();
        $this->assertNotNull($question);
        $this->assertEquals('fill_blank', $question->question_type);
        $this->assertTrue($question->isFillBlank());
        $this->assertEquals(20, $question->points);

        $options = $question->options()->orderBy('order_num')->get();
        $this->assertCount(2, $options);
        $this->assertEquals('20', $options[0]->option_text);
        $this->assertTrue((bool)$options[0]->is_correct);
        $this->assertEquals('10|sepuluh', $options[1]->option_text);
        $this->assertTrue((bool)$options[1]->is_correct);
    }

    public function test_admin_can_update_fill_in_the_blank_question(): void
    {
        $question = QuizQuestion::create([
            'quiz_id'       => $this->quiz->id,
            'question_type' => 'fill_blank',
            'points'        => 10,
            'question_text' => 'Nilai awal adalah [5]',
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => '5',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        $updatePayload = [
            'question_type' => 'fill_blank',
            'points'        => 15,
            'question_text' => 'Nilai awal adalah [5|lima] dan [15]',
            'options'       => [
                '5|lima',
                '15',
            ],
        ];

        $response = $this->actingAs($this->admin)->put(
            route('admin.quizzes.questions.update', [$this->quiz, $question]),
            $updatePayload
        );

        $response->assertSessionHas('success');

        $question->refresh();
        $this->assertEquals(15, $question->points);
        $this->assertEquals('Nilai awal adalah [5|lima] dan [15]', $question->question_text);
        $this->assertCount(2, $question->options);
    }

    public function test_play_arena_sanitizes_brackets_and_does_not_leak_keys_to_students(): void
    {
        $question = QuizQuestion::create([
            'quiz_id'       => $this->quiz->id,
            'question_type' => 'fill_blank',
            'points'        => 10,
            'question_text' => 'Apa output dari kode adalah [SECRET_KEY_123] dan [ANOTHER_KEY]',
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => 'SECRET_KEY_123',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => 'ANOTHER_KEY',
            'is_correct'       => true,
            'order_num'        => 2,
        ]);

        // Student attempt
        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '220101001',
            'student_name' => 'Budi Santoso',
            'attempt_num'  => 1,
            'status'       => 'in_progress',
            'started_at'   => now(),
            'total_points' => 10,
        ]);

        $response = $this->withSession([
            'quiz_nim'                      => '220101001',
            'quiz_nama'                     => 'Budi Santoso',
            'quiz_attempt_' . $this->quiz->id => $attempt->id,
            'attempt_id'                    => $attempt->id,
        ])->get(route('quiz.play', [$this->quiz->unique_token, $attempt->id]));

        $response->assertStatus(200);

        // Assert secret keys are NOT present in client response HTML or JSON
        $response->assertDontSee('SECRET_KEY_123');
        $response->assertDontSee('ANOTHER_KEY');

        // Assert sanitized placeholder [___] is present
        $response->assertSee('[___]');
    }

    public function test_scoring_service_awards_full_score_for_perfect_match(): void
    {
        $question = QuizQuestion::create([
            'quiz_id'       => $this->quiz->id,
            'question_type' => 'fill_blank',
            'points'        => 20,
            'question_text' => 'Output adalah [20] dan [10]',
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => '20',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => '10|sepuluh',
            'is_correct'       => true,
            'order_num'        => 2,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '220101002',
            'student_name' => 'Ani Wijaya',
            'attempt_num'  => 1,
            'status'       => 'in_progress',
            'started_at'   => now(),
            'total_points' => 20,
        ]);

        // User submits array answers
        $userAnswers = [
            $question->id => ['20', 'sepuluh'],
        ];

        QuizScoringService::finalize($attempt, $userAnswers);

        $attempt->refresh();
        $this->assertTrue($attempt->isCompleted());
        $this->assertEquals(100, $attempt->score); // 100%
        $this->assertEquals(20, $attempt->total_points_earned);
        $this->assertEquals(1, $attempt->correct_answers_count);
        $this->assertEquals(0, $attempt->wrong_answers_count);

        $ansRecord = $attempt->answers()->first();
        $this->assertNotNull($ansRecord);
        $this->assertTrue((bool)$ansRecord->is_correct);
        $this->assertEquals(20, $ansRecord->points_earned);
        $this->assertEquals('["20","sepuluh"]', $ansRecord->text_answer);
    }

    public function test_scoring_service_awards_partial_credit_when_one_of_two_blanks_is_correct(): void
    {
        $question = QuizQuestion::create([
            'quiz_id'       => $this->quiz->id,
            'question_type' => 'fill_blank',
            'points'        => 20,
            'question_text' => 'Output adalah [20] dan [10]',
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => '20',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => '10',
            'is_correct'       => true,
            'order_num'        => 2,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '220101003',
            'student_name' => 'Citra Dewi',
            'attempt_num'  => 1,
            'status'       => 'in_progress',
            'started_at'   => now(),
            'total_points' => 20,
        ]);

        // User gets 1st blank correct (20), but 2nd wrong (99)
        $userAnswers = [
            $question->id => ['20', '99'],
        ];

        QuizScoringService::finalize($attempt, $userAnswers);

        $attempt->refresh();
        $this->assertEquals(50, $attempt->score); // 1 out of 2 = 50%
        $this->assertEquals(10, $attempt->total_points_earned); // 10 out of 20 pts

        $ansRecord = $attempt->answers()->first();
        $this->assertNotNull($ansRecord);
        $this->assertFalse((bool)$ansRecord->is_correct); // not 100% correct
        $this->assertEquals(10, $ansRecord->points_earned);
    }

    public function test_scoring_is_case_insensitive_and_trims_whitespace(): void
    {
        $question = QuizQuestion::create([
            'quiz_id'       => $this->quiz->id,
            'question_type' => 'fill_blank',
            'points'        => 10,
            'question_text' => 'Bahasa pemrograman adalah [Python]',
            'order_num'     => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text'      => 'Python',
            'is_correct'       => true,
            'order_num'        => 1,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '220101004',
            'student_name' => 'Dedi Prasetyo',
            'attempt_num'  => 1,
            'status'       => 'in_progress',
            'started_at'   => now(),
            'total_points' => 10,
        ]);

        // Student writes in lowercase with surrounding spaces "   python  "
        $userAnswers = [
            $question->id => ['   python  '],
        ];

        QuizScoringService::finalize($attempt, $userAnswers);

        $attempt->refresh();
        $this->assertEquals(100, $attempt->score);
        $this->assertEquals(10, $attempt->total_points_earned);

        $ansRecord = $attempt->answers()->first();
        $this->assertTrue((bool)$ansRecord->is_correct);
        $this->assertEquals(10, $ansRecord->points_earned);
    }
}

