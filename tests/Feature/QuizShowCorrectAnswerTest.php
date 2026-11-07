<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizShowCorrectAnswerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $this->instructor = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_quiz_with_show_correct_answer_enabled_and_disabled(): void
    {
        // 1. Create with show_correct_answer = 1
        $res1 = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'               => 'Kuis Algoritma Pemrograman',
            'access_code'         => 'ALGO101',
            'show_review'         => '1',
            'show_correct_answer' => '1',
            'is_active'           => '1',
        ]);
        $res1->assertSessionHasNoErrors();

        $quiz1 = Quiz::where('access_code', 'ALGO101')->firstOrFail();
        $this->assertTrue($quiz1->show_correct_answer);
        $this->assertTrue($quiz1->canShowCorrectAnswer());

        // 2. Create without show_correct_answer (default off)
        $res2 = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'       => 'Kuis Jaringan Komputer',
            'access_code' => 'JARKOM101',
            'show_review' => '1',
            'is_active'   => '1',
        ]);
        $res2->assertSessionHasNoErrors();

        $quiz2 = Quiz::where('access_code', 'JARKOM101')->firstOrFail();
        $this->assertFalse($quiz2->show_correct_answer);
        $this->assertFalse($quiz2->canShowCorrectAnswer());
    }

    public function test_admin_can_update_quiz_show_correct_answer_setting(): void
    {
        $quiz = Quiz::create([
            'user_id'             => $this->admin->id,
            'title'               => 'Ujian Tengah Semester',
            'access_code'         => 'UTS-2026',
            'is_active'           => true,
            'show_review'         => true,
            'show_correct_answer' => false,
        ]);

        // Toggle to true
        $updateRes = $this->actingAs($this->admin)->put(route('admin.quizzes.update', $quiz), [
            'title'               => 'Ujian Tengah Semester (Updated)',
            'access_code'         => 'UTS-2026',
            'show_review'         => '1',
            'show_correct_answer' => '1',
            'is_active'           => '1',
        ]);
        $updateRes->assertSessionHasNoErrors();

        $quiz->refresh();
        $this->assertTrue($quiz->show_correct_answer);

        // Toggle back to false
        $updateRes2 = $this->actingAs($this->admin)->put(route('admin.quizzes.update', $quiz), [
            'title'               => 'Ujian Tengah Semester (Updated)',
            'access_code'         => 'UTS-2026',
            'show_review'         => '1',
            // omit show_correct_answer
            'is_active'           => '1',
        ]);
        $updateRes2->assertSessionHasNoErrors();

        $quiz->refresh();
        $this->assertFalse($quiz->show_correct_answer);
    }

    public function test_student_can_see_correct_answer_when_admin_enables_the_option(): void
    {
        $quiz = Quiz::create([
            'user_id'             => $this->admin->id,
            'title'               => 'Latihan Mandiri',
            'access_code'         => 'MANDIRI-1',
            'is_active'           => true,
            'session_status'      => 'started',
            'show_review'         => true,
            'show_correct_answer' => true, // ENABLED!
        ]);

        $question = $quiz->questions()->create([
            'question_text' => 'Jika variabel ipk bernilai 3.85, potongan yang didapatkan adalah?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
            'explanation'   => 'Potongan 10% berlaku untuk IPK di atas 3.75.',
        ]);

        $optA = $question->options()->create(['option_text' => '50%', 'is_correct' => false, 'order_num' => 1]);
        $optB = $question->options()->create(['option_text' => '30%', 'is_correct' => false, 'order_num' => 2]);
        $optC = $question->options()->create(['option_text' => '10%', 'is_correct' => true, 'order_num' => 3]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '12345678',
            'student_name'          => 'Ahmad Dani',
            'score'                 => 0.0,
            'total_points_earned'   => 0,
            'total_points_possible' => 10,
            'correct_answers_count' => 0,
            'wrong_answers_count'   => 1,
            'duration_seconds'      => 45,
            'completed_at'          => now(),
        ]);

        // Student chose Opt A (WRONG)
        $attempt->answers()->create([
            'quiz_question_id'   => $question->id,
            'selected_option_id' => $optA->id,
            'is_correct'         => false,
            'points_earned'      => 0,
        ]);

        $res = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $res->assertStatus(200);

        // Student should see their wrong answer marked
        $res->assertSee('Pilihan Anda (Salah)');
        $res->assertSee('Jawaban Salah');

        // And because show_correct_answer is TRUE, student CAN see the correct answer!
        $res->assertSee('Kunci Jawaban Benar:');
        $res->assertSee('10%');
        $res->assertSee('Kunci Benar');
        $res->assertSee('Potongan 10% berlaku untuk IPK di atas 3.75.');
    }

    public function test_student_cannot_see_correct_answer_when_admin_disables_the_option(): void
    {
        $quiz = Quiz::create([
            'user_id'             => $this->admin->id,
            'title'               => 'Ujian Resmi Lab',
            'access_code'         => 'UJIAN-LAB',
            'is_active'           => true,
            'session_status'      => 'started',
            'show_review'         => true,
            'show_correct_answer' => false, // DISABLED!
        ]);

        $question = $quiz->questions()->create([
            'question_text' => 'Apa kepanjangan dari CPU?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
            'explanation'   => 'CPU adalah Central Processing Unit.',
        ]);

        $optA = $question->options()->create(['option_text' => 'Computer Personal Unit', 'is_correct' => false, 'order_num' => 1]);
        $optB = $question->options()->create(['option_text' => 'Central Processing Unit', 'is_correct' => true, 'order_num' => 2]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '99887766',
            'student_name'          => 'Citra Kirana',
            'score'                 => 0.0,
            'total_points_earned'   => 0,
            'total_points_possible' => 10,
            'correct_answers_count' => 0,
            'wrong_answers_count'   => 1,
            'duration_seconds'      => 60,
            'completed_at'          => now(),
        ]);

        // Student chose Opt A (WRONG)
        $attempt->answers()->create([
            'quiz_question_id'   => $question->id,
            'selected_option_id' => $optA->id,
            'is_correct'         => false,
            'points_earned'      => 0,
        ]);

        $res = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $res->assertStatus(200);

        // Student still sees that their answer was wrong
        $res->assertSee('Pilihan Anda (Salah)');
        $res->assertSee('Jawaban Salah');
        $res->assertSee('Jawaban Anda belum tepat');

        // BUT the correct answer key and explanation must NOT be shown!
        $res->assertDontSee('Kunci Jawaban Benar:');
        $res->assertDontSee('Central Processing Unit adalah');
        $res->assertDontSee('Kunci Benar');
        $res->assertSee('Kunci jawaban yang benar disembunyikan oleh instruktur sesuai kebijakan kuis ini.');
    }

    public function test_student_answering_correctly_cannot_see_explanation_when_show_correct_answer_disabled(): void
    {
        $quiz = Quiz::create([
            'user_id'             => $this->admin->id,
            'title'               => 'Ujian Tertutup',
            'access_code'         => 'TUTUP-101',
            'is_active'           => true,
            'session_status'      => 'started',
            'show_review'         => true,
            'show_correct_answer' => false, // DISABLED!
        ]);

        $question = $quiz->questions()->create([
            'question_text' => 'Berapa hasil 5 + 5?',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
            'explanation'   => 'Penjelasan rahasia: 5 + 5 menghasilkan 10.',
        ]);

        $optA = $question->options()->create(['option_text' => '10', 'is_correct' => true, 'order_num' => 1]);
        $optB = $question->options()->create(['option_text' => '20', 'is_correct' => false, 'order_num' => 2]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '77889900',
            'student_name'          => 'Budi Santoso',
            'score'                 => 100.0,
            'total_points_earned'   => 10,
            'total_points_possible' => 10,
            'correct_answers_count' => 1,
            'wrong_answers_count'   => 0,
            'duration_seconds'      => 45,
            'completed_at'          => now(),
        ]);

        // Student chose Opt A (CORRECT!)
        $attempt->answers()->create([
            'quiz_question_id'   => $question->id,
            'selected_option_id' => $optA->id,
            'is_correct'         => true,
            'points_earned'      => 10,
        ]);

        $res = $this->withSession(['quiz_attempt_' . $quiz->id => $attempt->id])
            ->get(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $res->assertStatus(200);

        // Student sees they answered correctly
        $res->assertSee('Jawaban Anda Benar');
        $res->assertSee('+ 10 Poin');

        // BUT they must NOT see the explanation or the explicit "Jawaban Anda Benar: [A] 10" answer key summary
        $res->assertDontSee('Penjelasan rahasia: 5 + 5 menghasilkan 10.');
        $res->assertDontSee('Kunci Jawaban Benar:');
    }

    public function test_instructor_previewing_results_can_always_see_correct_answer_even_if_disabled(): void
    {
        $quiz = Quiz::create([
            'user_id'             => $this->instructor->id,
            'title'               => 'Ujian Khusus Asistensi',
            'access_code'         => 'ASIS-2026',
            'is_active'           => true,
            'session_status'      => 'started',
            'show_review'         => true,
            'show_correct_answer' => false, // Student cannot see
        ]);

        $question = $quiz->questions()->create([
            'question_text' => 'Soal Rahasia Lab',
            'question_type' => 'multiple_choice',
            'points'        => 10,
            'order_num'     => 1,
            'explanation'   => 'Kunci pembahasan lengkap instruktur.',
        ]);

        $optA = $question->options()->create(['option_text' => 'Pilihan Salah', 'is_correct' => false, 'order_num' => 1]);
        $optB = $question->options()->create(['option_text' => 'Pilihan Kunci Super Benar', 'is_correct' => true, 'order_num' => 2]);

        $attempt = QuizAttempt::create([
            'quiz_id'               => $quiz->id,
            'nim'                   => '11223344',
            'student_name'          => 'Doni Salmanan',
            'score'                 => 0.0,
            'total_points_earned'   => 0,
            'total_points_possible' => 10,
            'correct_answers_count' => 0,
            'wrong_answers_count'   => 1,
            'duration_seconds'      => 60,
            'completed_at'          => now(),
        ]);

        $attempt->answers()->create([
            'quiz_question_id'   => $question->id,
            'selected_option_id' => $optA->id,
            'is_correct'         => false,
            'points_earned'      => 0,
        ]);

        // Instructor viewing the results page
        $res = $this->actingAs($this->instructor)
            ->get(route('quiz.results', ['token' => $quiz->unique_token, 'attempt' => $attempt->id]));

        $res->assertStatus(200);

        // Instructor CAN see the correct key
        $res->assertSee('Kunci Jawaban Benar:');
        $res->assertSee('Pilihan Kunci Super Benar');
        $res->assertSee('Kunci pembahasan lengkap instruktur.');
    }
}
