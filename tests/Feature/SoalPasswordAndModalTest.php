<?php

namespace Tests\Feature;

use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoalPasswordAndModalTest extends TestCase
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

    public function test_admin_creating_soal_saves_plain_password_and_renders_on_show(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.soal.store'), [
            'title'                 => 'Praktikum Jaringan Komputer',
            'kode_unik'             => 'JK-MODUL-1',
            'password'              => 'Rahasia123',
            'access_type'           => 'lifetime',
            'is_active'             => '1',
            'is_downloadable'       => '1',
            'is_submission_enabled' => '0',
        ]);

        $response->assertRedirect(route('admin.soal.index'));

        $soal = Soal::where('kode_unik', 'JK-MODUL-1')->first();
        $this->assertNotNull($soal);
        $this->assertEquals('Rahasia123', $soal->plain_password);

        // View detail page
        $showResponse = $this->actingAs($this->admin)->get(route('admin.soal.show', $soal));
        $showResponse->assertOk();
        $showResponse->assertSee('Password Akses Dokumen');
        $showResponse->assertSee('id="editModal"', false);
        $showResponse->assertSee('openEditModal()', false);
    }

    public function test_admin_updating_soal_password_updates_plain_password(): void
    {
        $soal = Soal::create([
            'title'          => 'Alpro Modul 2',
            'kode_unik'      => 'ALPRO-2',
            'unique_token'   => 'token-1234567890ab',
            'password'       => bcrypt('Lama123'),
            'plain_password' => 'Lama123',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.soal.update', $soal), [
            'title'          => 'Alpro Modul 2 Revisi',
            'kode_unik'      => 'ALPRO-2',
            'password'       => 'Baru456',
            'access_type'    => 'lifetime',
            'is_active'      => '1',
            'is_downloadable'=> '1',
        ]);

        $response->assertRedirect(route('admin.soal.show', $soal));

        $soal->refresh();
        $this->assertEquals('Alpro Modul 2 Revisi', $soal->title);
        $this->assertEquals('Baru456', $soal->plain_password);
    }

    public function test_student_can_verify_access_with_password(): void
    {
        $soal = Soal::create([
            'title'          => 'Alpro Modul 3',
            'kode_unik'      => 'ALPRO-3',
            'unique_token'   => 'token-alpro3-sample',
            'password'       => bcrypt('AksesPass1'),
            'plain_password' => 'AksesPass1',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $verifyResponse = $this->post(route('soal.verify', $soal->unique_token), [
            'password' => 'AksesPass1',
        ]);

        $verifyResponse->assertRedirect(route('soal.view', $soal->unique_token));
    }

    public function test_admin_edit_route_redirects_to_show_with_open_edit_modal(): void
    {
        $soal = Soal::create([
            'title'          => 'Alpro Modul 4',
            'kode_unik'      => 'ALPRO-4',
            'unique_token'   => 'token-alpro4-sample',
            'password'       => bcrypt('AksesPass4'),
            'plain_password' => 'AksesPass4',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.soal.edit', $soal));
        $response->assertRedirect(route('admin.soal.show', $soal));
        $response->assertSessionHas('open_edit_modal', true);
    }

    public function test_admin_updating_soal_with_redirect_to_index(): void
    {
        $soal = Soal::create([
            'title'          => 'Alpro Modul 5',
            'kode_unik'      => 'ALPRO-5',
            'unique_token'   => 'token-alpro5-sample',
            'password'       => bcrypt('AksesPass5'),
            'plain_password' => 'AksesPass5',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.soal.update', $soal), [
            'redirect_to'    => 'index',
            'title'          => 'Alpro Modul 5 Terupdate',
            'kode_unik'      => 'ALPRO-5',
            'access_type'    => 'lifetime',
            'is_active'      => '1',
            'is_downloadable'=> '1',
        ]);

        $response->assertRedirect(route('admin.soal.index'));
        $response->assertSessionHas('success');

        $soal->refresh();
        $this->assertEquals('Alpro Modul 5 Terupdate', $soal->title);
    }

    public function test_admin_soal_index_renders_edit_modal_popup_and_trigger(): void
    {
        $soal = Soal::create([
            'title'          => 'Alpro Modul 6',
            'kode_unik'      => 'ALPRO-6',
            'unique_token'   => 'token-alpro6-sample',
            'password'       => bcrypt('AksesPass6'),
            'plain_password' => 'AksesPass6',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.soal.index'));
        $response->assertOk();
        $response->assertSee('id="editModal"', false);
        $response->assertSee('openEditModalForSoal(' . $soal->id . ')', false);
        $response->assertSee('modal-edit-soal-form', false);
    }

    public function test_soal_creates_slug_from_title_and_handles_collisions(): void
    {
        $soal1 = Soal::create([
            'title'          => 'Struktur Data Modul 1',
            'kode_unik'      => 'SD-01',
            'password'       => bcrypt('pass123'),
            'plain_password' => 'pass123',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $this->assertEquals('struktur-data-modul-1', $soal1->slug);

        $soal2 = Soal::create([
            'title'          => 'Struktur Data Modul 1',
            'kode_unik'      => 'SD-02',
            'password'       => bcrypt('pass123'),
            'plain_password' => 'pass123',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $this->assertEquals('struktur-data-modul-1-1', $soal2->slug);
    }

    public function test_student_accesses_soal_via_title_slug_and_password_gate_is_strictly_enforced(): void
    {
        $soal = Soal::create([
            'title'          => 'Ujian Praktikum Jaringan Komputer',
            'kode_unik'      => 'JARKOM-01',
            'password'       => bcrypt('RahasiaLab99'),
            'plain_password' => 'RahasiaLab99',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
            'content_text'   => 'Petunjuk Praktikum: Konfigurasikan VLAN dan OSPF.',
        ]);

        $this->assertEquals('ujian-praktikum-jaringan-komputer', $soal->slug);

        // 1. Mengakses path berbasis judul langsung membuka halaman login/password gate
        $accessRes = $this->get(route('soal.access', $soal->slug));
        $accessRes->assertOk();
        $accessRes->assertSee('Password');
        $accessRes->assertDontSee('Petunjuk Praktikum: Konfigurasikan VLAN dan OSPF.');

        // 2. Mengakses langsung ke /view tanpa input password ditolak dan dilempar kembali ke password gate
        $directViewRes = $this->get(route('soal.view', $soal->slug));
        $directViewRes->assertRedirect(route('soal.access', $soal->slug));

        // 3. Password salah ditolak
        $wrongPassRes = $this->post(route('soal.verify', $soal->slug), [
            'password' => 'SalahPassword',
        ]);
        $wrongPassRes->assertSessionHasErrors('password');

        // 4. Password benar memberi izin sesi dan mengarahkan ke /view
        $correctPassRes = $this->post(route('soal.verify', $soal->slug), [
            'password' => 'RahasiaLab99',
        ]);
        $correctPassRes->assertRedirect(route('soal.view', $soal->slug));

        // 5. Setelah verifikasi berhasil, halaman konten /view dapat dibuka
        $viewRes = $this->get(route('soal.view', $soal->slug));
        $viewRes->assertOk();
        $viewRes->assertSee('Petunjuk Praktikum: Konfigurasikan VLAN dan OSPF.');

        // 6. Akses lewat kode unik di welcome page juga diarahkan ke path berbasis slug judul
        $codeRes = $this->post(route('soal.accessByCode'), [
            'kode_unik' => 'JARKOM-01',
        ]);
        $codeRes->assertRedirect(route('soal.access', $soal->slug));
    }

    public function test_admin_soal_views_render_title_slug_in_copy_and_preview_links(): void
    {
        $soal = Soal::create([
            'title'          => 'Pemrograman Web Lanjut',
            'kode_unik'      => 'PWL-01',
            'password'       => bcrypt('pwd123'),
            'plain_password' => 'pwd123',
            'user_id'        => $this->admin->id,
            'access_type'    => 'lifetime',
            'is_active'      => true,
            'is_downloadable'=> true,
        ]);

        $this->assertEquals('pemrograman-web-lanjut', $soal->slug);

        // Di index dokumen
        $indexRes = $this->actingAs($this->admin)->get(route('admin.soal.index'));
        $indexRes->assertOk();
        $indexRes->assertSee(route('soal.access', 'pemrograman-web-lanjut'));

        // Di show dokumen
        $showRes = $this->actingAs($this->admin)->get(route('admin.soal.show', $soal));
        $showRes->assertOk();
        $showRes->assertSee(route('soal.access', 'pemrograman-web-lanjut'));
    }

    public function test_check_submission_endpoint_returns_submission_status_correctly(): void
    {
        $soal = Soal::create([
            'title'                 => 'Tugas Sistem Operasi',
            'user_id'               => $this->admin->id,
            'kode_unik'             => 'SO-001',
            'password'              => bcrypt('secret123'),
            'plain_password'        => 'secret123',
            'access_type'           => 'lifetime',
            'is_submission_enabled' => true,
        ]);

        // Belum terverifikasi sesi password -> 403
        $unauthRes = $this->get(route('soal.checkSubmission', ['token' => $soal->slug, 'nim' => '13020230001']));
        $unauthRes->assertStatus(403);

        // Verifikasi sesi password
        $this->withSession(["soal_access_id_{$soal->id}" => true]);

        // Belum ada data pengumpulan
        $notFoundRes = $this->get(route('soal.checkSubmission', ['token' => $soal->slug, 'nim' => '13020230001']));
        $notFoundRes->assertOk()
            ->assertJson(['has_submitted' => false]);

        // Buat submission
        \App\Models\Submission::create([
            'soal_id'            => $soal->id,
            'nim'                => '13020230001',
            'nama'               => 'Budi Santoso',
            'kelas'              => 'A1',
            'submission_type'    => 'link',
            'link_url'           => 'https://github.com/budi/os-assignment',
        ]);

        // Cek kembali dengan NIM yang sudah ada
        $foundRes = $this->get(route('soal.checkSubmission', ['token' => $soal->slug, 'nim' => '13020230001']));
        $foundRes->assertOk()
            ->assertJson([
                'has_submitted'   => true,
                'nama'            => 'Budi Santoso',
                'kelas'           => 'A1',
                'submission_type' => 'link',
            ]);
    }
}

