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
            'name' => 'Unique Cafés Especiais',
            'cnpj' => '99999999999999', // Exemplo
        ]);

    // 2. Cria um Perfil de Administrador com CONTROLE TOTAL
    $profile = Profile::create([
        'main_company_id' => $company->id,
        'name' => 'Administrador',
        'permissions' => [
            // Master
            'all_access' => true,
            
            // Módulos Operacionais
            'green_coffee_full' => true,
            'green_coffee_view' => true,
            'lab_full' => true,
            'lab_view' => true,
            'roast_full' => true,
            'roast_view' => true,
            
            // Cadastros e Gestão (Registries)
            'supplier_edit' => true,
            'supplier_delete' => true,
            'sample_edit' => true,
            'sample_delete' => true,
            'profile_edit' => true,
            'profile_delete' => true,
            'machine_edit' => true,
            'machine_delete' => true,
            
            // Permissão de time (Caso use no controller)
            'manage_team' => true 
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