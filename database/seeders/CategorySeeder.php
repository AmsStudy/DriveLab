<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'MODUL PRAKTIKUM', 'color' => '#F72585'], // pink accent
            ['name' => 'DATASET / SOURCE CODE', 'color' => '#A855F7'], // purple
            ['name' => 'TUGAS PENDAHULUAN', 'color' => '#3B82F6'], // blue
            ['name' => 'SOAL EVALUASI', 'color' => '#F59E0B'], // yellow/amber
            ['name' => 'MATERI ASISTENSI', 'color' => '#10B981'], // green
        ];

        $user = \App\Models\User::first();
        $adminId = $user ? $user->id : 1;

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'color' => $cat['color'],
                    'description' => 'Kategori untuk ' . strtolower($cat['name']),
                    'created_by' => $adminId
                ]
            );
        }
    }
}
