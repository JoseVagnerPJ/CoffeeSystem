<?php

namespace Database\Seeders;

use App\Models\MainCompany;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companyNames = [
            'Café Aurora',
            'Cafés da Serra',
            'Grão Nobre',
            'Fazenda Horizonte',
            'Café Vale Dourado',
            'Terra Alta Cafés',
        ];

        $company = MainCompany::firstOrNew(['cnpj' => '99999999999999']);
        $company->fill([
            'name' => $companyNames[array_rand($companyNames)] . ' ' . random_int(100, 999),
            'is_active' => true,
            'mod_lab' => true,
            'mod_stock' => true,
            'mod_roast' => true,
            'mod_prod' => true,
            'mod_logistics' => true,
        ]);
        $company->save();

        $profile = Profile::updateOrCreate(
            [
                'main_company_id' => $company->id,
                'name' => 'Desenvolvedor',
            ],
            [
                'permissions' => [
                    'dev_access' => true,
                    'all_access' => true,
                ],
            ]
        );

        User::updateOrCreate(
            [
                'main_company_id' => $company->id,
                'username' => 'zevagner',
            ],
            [
                'name' => 'Zevagner',
                'profile_id' => $profile->id,
                'password' => Hash::make('1572'),
                'is_active' => true,
            ]
        );

        if ($this->command) {
            $this->command->info('Empresa de demonstração criada. Acesso: CNPJ 99999999999999, usuário zevagner.');
        }
    }
}
