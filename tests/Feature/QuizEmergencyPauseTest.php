<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizEmergencyPauseTest extends TestCase
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
            'title'              => 'Ujian Praktikum Sistem Operasi',
            'session_status'     => 'started',
            'starts_at'          => now()->subMinutes(10),
            'time_limit_minutes' => 30,
            'is_active'          => true,
            'max_attempts'       => 1,
        ]);
    }

    public function test_admin_can_pause_active_session(): void
    {
        $response = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.session.pause', $this->quiz)
        );

        $response->assertOk()
            ->assertJson([
                'success'        => true,
                'session_status' => 'paused',
            ]);

        $this->quiz->refresh();
        $this->assertEquals('paused', $this->quiz->session_status);
        $this->assertNotNull($this->quiz->paused_at);
        $this->assertTrue($this->quiz->isSessionPaused());
    }

    public function test_student_session_check_freezes_timer_when_paused(): void
    {
        // Set quiz to paused 5 minutes after start
        $pausedAt = now()->subMinutes(5);
        $this->quiz->update([
            'session_status' => 'paused',
            'paused_at'      => $pausedAt,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'            => $this->quiz->id,
            'nim'                => '13020210090',
            'student_name'       => 'Rizky Pratama',
            'started_at'         => now()->subMinutes(10), // started 10m ago, paused 5m ago => 5m active work elapsed
            'extra_time_minutes' => 0,
        ]);

        $response = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->getJson(route('quiz.session.check', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]));

        $response->assertOk()
            ->assertJsonPath('status', 'paused')
            ->assertJsonPath('is_paused', true);

        // 30 min quiz, 5m active work up to paused_at => remaining should be approx 25 min (1500s)
        $timeRemaining = $response->json('time_remaining');
        $this->assertGreaterThan(1450, $timeRemaining);
        $this->assertLessThanOrEqual(1500, $timeRemaining);
    }

    public function test_student_autosave_and_submit_blocked_during_pause(): void
    {
        $this->quiz->update([
            'session_status' => 'paused',
            'paused_at'      => now(),
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '13020210091',
            'student_name' => 'Fajar Nugraha',
            'started_at'   => now()->subMinutes(5),
        ]);

        // Autosave should report paused
        $resAutosave = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->postJson(route('quiz.autosave', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]), ['answers' => ['1' => '2']]);

        $resAutosave->assertOk()
            ->assertJson([
                'success'   => false,
                'is_paused' => true,
            ]);

        // Submit should be rejected
        $resSubmit = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->postJson(route('quiz.submit', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]), ['answers' => []]);

        $resSubmit->assertStatus(422)
            ->assertJson([
                'success'   => false,
                'is_paused' => true,
            ]);
    }

    public function test_tab_switch_suppressed_without_penalty_during_pause(): void
    {
        $this->quiz->update([
            'session_status' => 'paused',
            'paused_at'      => now(),
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'          => $this->quiz->id,
            'nim'              => '13020210092',
            'student_name'     => 'Anisa Rahma',
            'started_at'       => now()->subMinutes(5),
            'tab_switch_count' => 0,
        ]);

        $response = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->postJson(route('quiz.tab-switch', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]), ['reason' => 'alt_tab_switch']);

        $response->assertOk()
            ->assertJson([
                'success'         => true,
                'is_paused'       => true,
                'penalty_seconds' => 0,
            ]);

        $attempt->refresh();
        $this->assertEquals(0, $attempt->tab_switch_count);
        $this->assertFalse((bool) $attempt->is_locked);
    }

    public function test_admin_can_resume_session_and_compensates_active_attempts(): void
    {
        // Simulate quiz was paused 5 minutes (300 seconds) ago
        $pausedAt = now()->subMinutes(5);
        $this->quiz->update([
            'session_status' => 'paused',
            'paused_at'      => $pausedAt,
        ]);

        $originalStartedAt = now()->subMinutes(15);
        $attempt = QuizAttempt::create([
            'quiz_id'      => $this->quiz->id,
            'nim'          => '13020210093',
            'student_name' => 'Bayu Wicaksono',
            'started_at'   => $originalStartedAt,
        ]);

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.session.resume', $this->quiz)
        );

        $response->assertOk()
            ->assertJson([
                'success'        => true,
                'session_status' => 'started',
            ]);

        $this->quiz->refresh();
        $this->assertEquals('started', $this->quiz->session_status);
        $this->assertNull($this->quiz->paused_at);

        $attempt->refresh();
        // The attempt's started_at must be shifted forward by approximately 300 seconds
        $shiftSeconds = (int) $originalStartedAt->diffInSeconds($attempt->started_at);
        $this->assertGreaterThanOrEqual(295, $shiftSeconds);
        $this->assertLessThanOrEqual(305, $shiftSeconds);
    }

    public function test_unauthorized_user_cannot_pause_or_resume(): void
    {
        $otherUser = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        // Guest pause
        $this->postJson(route('admin.quizzes.session.pause', $this->quiz))
            ->assertUnauthorized();

        // Guest resume
        $this->postJson(route('admin.quizzes.session.resume', $this->quiz))
            ->assertUnauthorized();

        // Other admin pause
        $this->actingAs($otherUser)->postJson(route('admin.quizzes.session.pause', $this->quiz))
            ->assertForbidden();

        // Other admin resume
        $this->actingAs($otherUser)->postJson(route('admin.quizzes.session.resume', $this->quiz))
            ->assertForbidden();
    }
}
