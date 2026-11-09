<?php

namespace Tests\Feature;

use App\Models\Soal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SoalSubmissionsAutoRefreshTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Soal $soal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $this->soal = Soal::create([
            'user_id'               => $this->admin->id,
            'title'                 => 'Praktikum Cloud Computing',
            'kode_unik'             => 'CLOUD-001',
            'unique_token'          => 'cloud123',
            'password'              => Hash::make('rahasia123'),
            'plain_password'        => 'rahasia123',
            'access_type'           => 'lifetime',
            'is_active'             => true,
            'is_submission_enabled' => true,
            'classes'               => ['TI-A', 'TI-B'],
        ]);
    }

    public function test_show_page_renders_refresh_button_and_live_badge(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.soal.show', $this->soal));

        $response->assertOk();
        $response->assertSee('Riwayat Pengumpulan Tugas');
        $response->assertSee('Live 3s');
        $response->assertSee('Segarkan');
        $response->assertSee('id="btnRefreshSubmissions"', false);
        $response->assertSee('id="submissionsContainer"', false);
        $response->assertSee('checkSubmissionsUpdate', false);
    }

    public function test_submissions_data_endpoint_returns_json_with_html_when_no_checksum_provided(): void
    {
        Submission::create([
            'soal_id'         => $this->soal->id,
            'nim'             => '220001',
            'nama'            => 'Budi Santoso',
            'kelas'           => 'TI-A',
            'submission_type' => 'text',
            'text_content'    => 'Ini jawaban teks tugas praktikum.',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', $this->soal));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'changed' => true,
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['checksum']);
        $this->assertEquals(1, $json['counts']['total']);
        $this->assertEquals(1, $json['counts']['text']);
        $this->assertEquals(0, $json['counts']['file']);
        $this->assertEquals(1, $json['counts']['classes']['TI-A']);
        $this->assertStringContainsString('Budi Santoso', $json['html']);
        $this->assertStringContainsString('220001', $json['html']);
    }

    public function test_submissions_data_endpoint_returns_changed_false_when_checksum_matches(): void
    {
        $sub = Submission::create([
            'soal_id'         => $this->soal->id,
            'nim'             => '220002',
            'nama'            => 'Siti Nurhaliza',
            'kelas'           => 'TI-B',
            'submission_type' => 'link',
            'link_url'        => 'https://github.com/siti/project',
        ]);

        // Get initial checksum
        $initial = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', $this->soal));

        $initialChecksum = $initial->json('checksum');
        $this->assertNotEmpty($initialChecksum);

        // Next poll with matching checksum
        $poll = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', ['soal' => $this->soal, 'checksum' => $initialChecksum]));

        $poll->assertOk();
        $poll->assertJson([
            'success' => true,
            'changed' => false,
            'checksum' => $initialChecksum,
        ]);
        $this->assertArrayNotHasKey('html', $poll->json());
    }

    public function test_submissions_data_endpoint_returns_changed_true_when_force_is_true(): void
    {
        Submission::create([
            'soal_id'         => $this->soal->id,
            'nim'             => '220003',
            'nama'            => 'Andi Pratama',
            'kelas'           => 'TI-A',
            'submission_type' => 'link',
            'link_url'        => 'https://gitlab.com/andi/cloud',
        ]);

        $initial = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', $this->soal));

        $checksum = $initial->json('checksum');

        // Forced reload
        $forcePoll = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', ['soal' => $this->soal, 'checksum' => $checksum, 'force' => 1]));

        $forcePoll->assertOk();
        $forcePoll->assertJson([
            'success' => true,
            'changed' => true,
            'checksum' => $checksum,
        ]);
        $this->assertArrayHasKey('html', $forcePoll->json());
        $this->assertStringContainsString('Andi Pratama', $forcePoll->json('html'));
    }

    public function test_checksum_changes_when_new_submission_added(): void
    {
        $res1 = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', $this->soal));
        $checksum1 = $res1->json('checksum');

        // Add submission
        Submission::create([
            'soal_id'         => $this->soal->id,
            'nim'             => '220004',
            'nama'            => 'Dewi Lestari',
            'kelas'           => 'TI-A',
            'submission_type' => 'text',
            'text_content'    => 'Jawaban tugas dewi',
        ]);

        $res2 = $this->actingAs($this->admin)
            ->getJson(route('admin.soal.submissions-data', ['soal' => $this->soal, 'checksum' => $checksum1]));

        $this->assertNotEquals($checksum1, $res2->json('checksum'));
        $res2->assertJson([
            'success' => true,
            'changed' => true,
        ]);
        $this->assertEquals(1, $res2->json('counts.total'));
    }

    public function test_unauthorized_user_cannot_fetch_submissions_data(): void
    {
        $otherAdmin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($otherAdmin)
            ->getJson(route('admin.soal.submissions-data', $this->soal));

        $response->assertForbidden();
    }
}
