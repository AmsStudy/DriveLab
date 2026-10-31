<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizClassFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_quiz_with_classes(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.quizzes.store'), [
            'title'        => 'Kuis Jaringan Komputer',
            'access_code'  => 'JK-MODUL-1',
            'classes'      => 'A1, A2, B1',
            'max_attempts' => 1,
            'is_active'    => 1,
        ]);

        $quiz = Quiz::where('access_code', 'JK-MODUL-1')->first();
        $this->assertNotNull($quiz);
        $this->assertTrue($quiz->hasClasses());
        $this->assertEquals(['A1', 'A2', 'B1'], $quiz->getClassesList());

        $response->assertRedirect(route('admin.quizzes.questions', $quiz));
    }

    public function test_admin_can_update_quiz_classes(): void
    {
        $quiz = Quiz::create([
            'user_id'      => $this->admin->id,
            'title'        => 'Kuis Algoritma',
            'access_code'  => 'ALGO-1',
            'classes'      => ['A1'],
            'max_attempts' => 1,
            'is_active'    => true,
        ]);

        $this->actingAs($this->admin)->put(route('admin.quizzes.update', $quiz), [
            'title'        => 'Kuis Algoritma Revisi',
            'access_code'  => 'ALGO-1',
            'classes'      => 'A1, A2, C1',
            'max_attempts' => 1,
            'is_active'    => 1,
        ]);

        $quiz->refresh();
        $this->assertEquals(['A1', 'A2', 'C1'], $quiz->getClassesList());
    }

    public function test_student_must_select_class_if_quiz_has_classes(): void
    {
        $quiz = Quiz::create([
            'user_id'      => $this->admin->id,
            'title'        => 'Kuis Basis Data',
            'access_code'  => 'BD-01',
            'unique_token' => 'token-bd-01',
            'classes'      => ['A1', 'A2'],
            'max_attempts' => 1,
            'is_active'    => true,
        ]);

        // Attempt without class should fail validation
        $response = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'  => '13020230001',
            'nama' => 'Budi Santoso',
        ]);

        $response->assertSessionHasErrors('kelas');
        $this->assertDatabaseMissing('quiz_attempts', ['nim' => '13020230001']);
    }

    public function test_student_can_start_quiz_with_valid_class(): void
    {
        $quiz = Quiz::create([
            'user_id'      => $this->admin->id,
            'title'        => 'Kuis Pemrograman Web',
            'access_code'  => 'PW-01',
            'unique_token' => 'token-pw-01',
            'classes'      => ['A1', 'A2'],
            'max_attempts' => 1,
            'is_active'    => true,
        ]);

        QuizQuestion::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Apa kepanjangan dari HTML?',
            'question_type' => 'single',
            'points'        => 10,
            'order_num'     => 1,
        ]);

        $response = $this->post(route('quiz.start', $quiz->unique_token), [
            'nim'   => '13020230002',
            'nama'  => 'Dewi Lestari',
            'kelas' => 'A2',
        ]);

        $attempt = QuizAttempt::where('nim', '13020230002')->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('A2', $attempt->kelas);

        $response->assertRedirect(route('quiz.play', [
            'token'   => $quiz->unique_token,
            'attempt' => $attempt->id,
        ]));
    }

    public function test_admin_can_export_quiz_csv_filtered_by_class(): void
    {
        $quiz = Quiz::create([
            'user_id'      => $this->admin->id,
            'title'        => 'Kuis Multimedia',
            'access_code'  => 'MM-01',
            'slug'         => 'kuis-multimedia',
            'unique_token' => 'token-mm-01',
            'classes'      => ['A1', 'A2'],
            'max_attempts' => 1,
            'is_active'    => true,
        ]);

        QuizAttempt::create([
            'quiz_id'      => $quiz->id,
            'nim'          => '13020230010',
            'student_name' => 'Peserta A1',
            'kelas'        => 'A1',
            'score'        => 90,
            'completed_at' => now(),
            'started_at'   => now()->subMinutes(10),
        ]);

        QuizAttempt::create([
            'quiz_id'      => $quiz->id,
            'nim'          => '13020230020',
            'student_name' => 'Peserta A2',
            'kelas'        => 'A2',
            'score'        => 80,
            'completed_at' => now(),
            'started_at'   => now()->subMinutes(12),
        ]);

        // Export only Class A1
        $responseA1 = $this->actingAs($this->admin)->get(route('admin.quizzes.export-csv', [
            'quiz'  => $quiz,
            'kelas' => 'A1',
        ]));

        $responseA1->assertOk();
        $contentA1 = $responseA1->streamedContent();
        $this->assertStringContainsString('Peserta A1', $contentA1);
        $this->assertStringNotContainsString('Peserta A2', $contentA1);

        // Export all classes
        $responseAll = $this->actingAs($this->admin)->get(route('admin.quizzes.export-csv', [
            'quiz' => $quiz,
        ]));

        $responseAll->assertOk();
        $contentAll = $responseAll->streamedContent();
        $this->assertStringContainsString('Peserta A1', $contentAll);
        $this->assertStringContainsString('Peserta A2', $contentAll);
    }
}
