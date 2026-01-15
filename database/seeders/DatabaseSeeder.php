<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCompany;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Cria a Empresa Principal
        $company = MainCompany::create([
            'name' => 'Unique Cafés Especiais',
            'cnpj' => '99999999999999',
        ]);

        // 2. Cria o Perfil de DESENVOLVEDOR com todas as chaves
        $devProfile = Profile::create([
            'main_company_id' => $company->id,
            'name' => 'Desenvolvedor',
            'permissions' => [
                'dev_access' => true,      // Chave especial de desenvolvedor
                'all_access' => true,      // Controle Total (Cadeado Master)
                
                // Módulos Operacionais
                'green_coffee_full' => true,
                'green_coffee_view' => true,
                'lab_full'          => true,
                'lab_view'          => true,
                'roast_full'        => true,
                'roast_view'        => true,
                
                // Gestão de Equipe
                'team_full'         => true,
                'team_user_only'    => true,

                // Cadastros
                'supplier_full'     => true,
                'supplier_view'     => true,
                'sample_full'       => true,
                'sample_view'       => true,
                'machine_full'      => true,
                'machine_view'      => true,
                'sensory_full'      => true,
                'sensory_view'      => true,

                // Configurações
                'settings_edit'     => true,
            ]
        ]);

        // 3. Cria o seu Usuário (zevagner)
        User::create([
            'main_company_id' => $company->id,
            'profile_id'      => $devProfile->id,
            'name'            => 'José Vagner',
            'username'        => 'zevagner',
            'password'        => Hash::make('1572'), // Sua senha solicitada
            'is_active'       => true,
        ]);
    }
}