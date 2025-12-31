<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCompany;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Cria a Empresa Principal (CNPJ sem pontos no banco)
        $company = MainCompany::create([
            'name' => 'Unique Cafés Especialidade',
            'cnpj' => '12345678000199', // Exemplo
        ]);

        // 2. Cria um Perfil de Administrador
        $profile = Profile::create([
            'main_company_id' => $company->id,
            'name' => 'Administrador',
            'permissions' => [
                'manage_team' => true,
                'edit_suppliers' => true,
                'view_sensory_profiles' => true
            ]
        ]);

        // 3. Cria o Usuário Administrador
        User::create([
            'main_company_id' => $company->id,
            'profile_id' => $profile->id,
            'name' => 'José Vagner',
            'username' => 'zevagner',
            'password' => Hash::make('123'), // Senha simples para teste conforme solicitado
            'is_active' => true,
        ]);
    }
}