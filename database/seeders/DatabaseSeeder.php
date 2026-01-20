<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCompany;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Criar a Empresa Master
        $company = MainCompany::create([
            'name'      => 'Unique Cafés Especiais',
            'cnpj'      => '99999999999999',
            'is_active' => true,
            'mod_lab'   => true,
            'mod_stock' => true,
            'mod_roast' => false,
            'mod_prod'  => false,
        ]);

        // 2. Criar o Perfil de Desenvolvedor com todas as permissões
        $profile = Profile::create([
            'main_company_id' => $company->id,
            'name' => 'Desenvolvedor',
            'permissions' => [
                'dev_access' => true,
                'all_access' => true,
                // Adicione outras aqui se quiser
            ]
        ]);

        // 3. Criar o seu Usuário (Removido o campo 'role')
        User::create([
            'name'            => 'José Vagner',
            'username'        => 'zevagner',
            'password'        => Hash::make('1572'),
            'main_company_id' => $company->id,
            'profile_id'      => $profile->id,
            'is_active'       => true,
        ]);

        $this->command->info('Ambiente MASTER configurado com sucesso!');
    }
}