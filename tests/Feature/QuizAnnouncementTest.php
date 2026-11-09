<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAnnouncement;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAnnouncementTest extends TestCase
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
            'title'              => 'Kuis Praktikum Jaringan Komputer',
            'session_status'     => 'started',
            'time_limit_minutes' => 30,
            'is_active'          => true,
            'max_attempts'       => 1,
        ]);
    }

    public function test_admin_can_broadcast_announcement(): void
    {
        $response = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.announcements.store', $this->quiz),
            [
                'message' => 'Perhatian: Soal No. 5 opsi C ada ralat typo, gunakan satuan Mbps bukan Kbps.',
                'type'    => 'warning',
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Pengumuman berhasil disiarkan ke seluruh mahasiswa!',
            ])
            ->assertJsonPath('announcement.message', 'Perhatian: Soal No. 5 opsi C ada ralat typo, gunakan satuan Mbps bukan Kbps.')
            ->assertJsonPath('announcement.type', 'warning');

        $this->assertDatabaseHas('quiz_announcements', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->admin->id,
            'message' => 'Perhatian: Soal No. 5 opsi C ada ralat typo, gunakan satuan Mbps bukan Kbps.',
            'type'    => 'warning',
        ]);
    }

    public function test_unauthorized_user_cannot_broadcast_announcement(): void
    {
        $otherUser = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        // Guest attempt
        $resGuest = $this->postJson(
            route('admin.quizzes.announcements.store', $this->quiz),
            ['message' => 'Pesan dari hacker', 'type' => 'urgent']
        );
        $resGuest->assertUnauthorized();

        // Other admin attempt (not the owner and not superadmin)
        $resOther = $this->actingAs($otherUser)->postJson(
            route('admin.quizzes.announcements.store', $this->quiz),
            ['message' => 'Pesan dari admin lain', 'type' => 'urgent']
        );
        $resOther->assertForbidden();
    }

    public function test_broadcast_announcement_validation_rules(): void
    {
        // Empty message
        $resEmpty = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.announcements.store', $this->quiz),
            ['message' => '', 'type' => 'info']
        );
        $resEmpty->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        // Invalid type
        $resType = $this->actingAs($this->admin)->postJson(
            route('admin.quizzes.announcements.store', $this->quiz),
            ['message' => 'Pesan valid', 'type' => 'invalid_type']
        );
        $resType->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_live_monitor_data_includes_announcements(): void
    {
        $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Sisa waktu 15 menit lagi.',
            'type'      => 'info',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson(
            route('admin.quizzes.monitor.data', $this->quiz)
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'announcements')
            ->assertJsonPath('announcements.0.message', 'Sisa waktu 15 menit lagi.')
            ->assertJsonPath('announcements.0.type', 'info');
    }

    public function test_projector_data_includes_latest_announcement(): void
    {
        $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Pengumuman lama',
            'type'      => 'info',
            'is_active' => true,
        ]);

        $latest = $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Ralat nomor 3',
            'type'      => 'warning',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson(
            route('admin.quizzes.projector.data', $this->quiz)
        );

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('latest_announcement.id', $latest->id)
            ->assertJsonPath('latest_announcement.message', 'Ralat nomor 3')
            ->assertJsonPath('latest_announcement.type', 'warning');
    }

    public function test_student_receives_announcements_via_session_check(): void
    {
        $announcement = $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Harap tetap tenang di ruang lab.',
            'type'      => 'info',
            'is_active' => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id'          => $this->quiz->id,
            'nim'              => '13020210088',
            'student_name'     => 'Ahmad Fathir',
            'started_at'       => now(),
            'tab_switch_count' => 0,
        ]);

        $response = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->getJson(route('quiz.session.check', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]));

        $response->assertOk()
            ->assertJsonPath('status', 'active')
            ->assertJsonPath('latest_announcement.id', $announcement->id)
            ->assertJsonPath('latest_announcement.message', 'Harap tetap tenang di ruang lab.')
            ->assertJsonPath('announcements.0.message', 'Harap tetap tenang di ruang lab.');
    }

    public function test_student_play_view_receives_initial_announcements(): void
    {
        $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Selamat mengerjakan kuis praktikum.',
            'type'      => 'info',
            'is_active' => true,
        ]);

        $q = $this->quiz->questions()->create([
            'question_text' => 'Soal 1',
            'question_type' => 'multiple_choice',
            'points'        => 10,
        ]);
        $q->options()->create(['option_text' => 'Opsi A', 'is_correct' => true, 'order_num' => 1]);

        $attempt = QuizAttempt::create([
            'quiz_id'          => $this->quiz->id,
            'nim'              => '13020210089',
            'student_name'     => 'Siti Nurhaliza',
            'started_at'       => now(),
            'tab_switch_count' => 0,
        ]);

        $response = $this->withSession(['quiz_attempt_' . $this->quiz->id => $attempt->id])
            ->get(route('quiz.play', [
                'token'   => $this->quiz->unique_token,
                'attempt' => $attempt->id,
            ]));

        $response->assertOk()
            ->assertViewHas('initialAnnouncements', function ($announcements) {
                return count($announcements) === 1 && $announcements[0]['message'] === 'Selamat mengerjakan kuis praktikum.';
            });
    }

    public function test_admin_can_delete_announcement(): void
    {
        $announcement = $this->quiz->announcements()->create([
            'user_id'   => $this->admin->id,
            'message'   => 'Pengumuman typo yang salah kirim.',
            'type'      => 'info',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(
            route('admin.quizzes.announcements.destroy', [
                'quiz'         => $this->quiz,
                'announcement' => $announcement,
            ])
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('quiz_announcements', [
            'id' => $announcement->id,
        ]);
    }
}

