<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    public function test_404_page_renders_custom_view(): void
    {
        $response = $this->get('/non-existent-page-random-xyz-12345');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('IC-LABS');
        $response->assertSee('Ke Beranda');
    }

    public function test_all_custom_error_views_can_be_rendered(): void
    {
        $views = [
            'errors.403' => ['403', 'Akses Tidak Diizinkan'],
            'errors.404' => ['404', 'Halaman Tidak Ditemukan'],
            'errors.419' => ['419', 'Sesi Anda Telah Berakhir'],
            'errors.429' => ['429', 'Batas Permintaan Terlampaui'],
            'errors.500' => ['500', 'Terjadi Kesalahan pada Server'],
            'errors.503' => ['503', 'Layanan Sedang dalam Pemeliharaan'],
        ];

        foreach ($views as $viewName => [$code, $heading]) {
            $rendered = view($viewName)->render();
            $this->assertStringContainsString($code, $rendered);
            $this->assertStringContainsString($heading, $rendered);
            $this->assertStringContainsString('IC-LABS', $rendered);
            $this->assertStringContainsString('Ke Beranda', $rendered);
        }
    }
}
