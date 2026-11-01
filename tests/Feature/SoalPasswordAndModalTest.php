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
}

