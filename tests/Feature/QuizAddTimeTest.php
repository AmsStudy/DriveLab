<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAddTimeTest extends TestCase
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
            'user_id'            => $this->admin->id,
            'title'              => 'Ujian Pemrograman Web',
            'session_status'     => 'started',
            'time_limit_minutes' => 30,
            'is_active'          => true,
            'max_attempts'       => 1,
        ]);
    }

    public function test_admin_can_add_extra_time_to_active_attempt(): void
    {
        $attempt = QuizAttempt::create([
            'quiz_id'            => $this->quiz->id,
            'nim'                => '13020210055',
            'student_name'       => 'Budi Santoso',
            'started_at'         => now()->subMinutes(10),
            'extra_time_minutes' => 0,
        ]);

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.attempts.add-time', [
                'quiz'    => $this->quiz,
                'attempt' => $attempt,
            ]),
            ['minutes' => 10]
        );

        $response->assertOk()
            ->assertJson([
                'success'            => true,
                'extra_time_minutes' => 10,
            ]);

        $attempt->refresh();
        $this->assertEquals(10, $attempt->extra_time_minutes);

        // Verify audit log
        $this->assertNotNull($attempt->violations_log);
        $this->assertEquals('extra_time_added_by_admin', $attempt->violations_log[0]['type']);
        $this->assertEquals('+10 menit', $attempt->violations_log[0]['added']);
    }

    public function test_cannot_add_time_to_completed_attempt(): void
    {
        $attempt = QuizAttempt::create([
            'quiz_id'            => $this->quiz->id,
            'nim'                => '13020210056',
            'student_name'       => 'Andi Wijaya',
            'started_at'         => now()->subMinutes(30),
            'completed_at'       => now(),
            'score'              => 85,
            'extra_time_minutes' => 0,
        ]);

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.attempts.add-time', [
                'quiz'    => $this->quiz,
                'attempt' => $attempt,
            ]),
            ['minutes' => 5]
        );

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Sesi mahasiswa ini telah selesai.',
            ]);
    }

    public function test_student_session_check_reflects_added_extra_time(): void
    {
        $attempt = QuizAttempt::create([
            'quiz_id'            => $this->quiz->id,
            'nim'                => '13020210057',
            'student_name'       => 'Dewi Lestari',
            'started_at'         => now()->subMinutes(20), // 10 mins remaining in 30-min quiz
            'extra_time_minutes' => 5, // added 5 mins extra => total allowed 35 mins => 15 mins remaining (900s)
        ]);

        $response = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->getJson(route('quiz.session.check', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]));

        $response->assertOk()
            ->assertJsonPath('status', 'active')
            ->assertJsonPath('extra_time_minutes', 5);

        $timeRemaining = $response->json('time_remaining');
        // Elapsed is 20m (1200s). Total allowed is 35m (2100s). Remaining should be around 900s (15 min)
        $this->assertGreaterThan(850, $timeRemaining);
        $this->assertLessThanOrEqual(900, $timeRemaining);
    }
}
