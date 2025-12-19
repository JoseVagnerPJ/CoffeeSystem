<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria a Empresa Mãe
        $company = Company::create([
            'name' => 'Torrefação Central',
            'cnpj' => '12.345.678/0001-99',
        ]);

        // 2. Cria o Usuário Administrador vinculado a essa empresa
        User::create([
            'company_id' => $company->id,
            'name' => 'Admin Coffee',
            'email' => 'admin@coffee.com',
            'password' => Hash::make('123456'), // Senha criptografada
            'role' => 'admin',
        ]);
        
        // 3. Cria uma Máquina de teste para vermos no dashboard
        \DB::table('roasting_machines')->insert([
            'company_id' => $company->id,
            'name' => 'Probat L25',
            'max_capacity' => 25.00,
            'maintenance_limit' => 40,
            'current_cycles' => 0,
        ]);
    }
}