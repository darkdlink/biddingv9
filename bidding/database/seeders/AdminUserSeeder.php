<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistemabidding.com.br',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin_sistema',
            'is_active' => true,
        ]);
    }
}
