<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@labsoal.com'],
            [
                'name'        => 'Super Admin',
                'email'       => 'superadmin@labsoal.com',
                'password'    => Hash::make('superadmin123'),
                'role'        => 'superadmin',
                'status'      => 'active',
                'institution' => 'Laboratorium Pusat',
            ]
        );

        $this->command->info('Super Admin berhasil dibuat!');
        $this->command->info('Email: superadmin@labsoal.com');
        $this->command->info('Password: superadmin123');
    }
}
