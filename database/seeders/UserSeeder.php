<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@essalud.pe',
            'password' => bcrypt('Admin123!'),
            'full_name' => 'Administrador del Sistema',
            'role' => 'SADM',
            'is_active' => true,
        ]);
        $admin->assignRole('SADM');

        $aseg = User::create([
            'name' => 'Asegurado',
            'email' => 'aseg@essalud.pe',
            'password' => bcrypt('Aseg123!'),
            'full_name' => 'Juan Asegurado Perez',
            'dni' => '12345678',
            'phone' => '999888777',
            'role' => 'ASEG',
            'is_active' => true,
        ]);
        $aseg->assignRole('ASEG');

        $oper = User::create([
            'name' => 'Operador',
            'email' => 'oper@essalud.pe',
            'password' => bcrypt('Oper123!'),
            'full_name' => 'Maria Operadora Lopez',
            'dni' => '87654321',
            'phone' => '999777666',
            'role' => 'OPER',
            'is_active' => true,
        ]);
        $oper->assignRole('OPER');
    }
}
