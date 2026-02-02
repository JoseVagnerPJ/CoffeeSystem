<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MainCompany;
use App\Models\User;
use App\Models\Profile;
use App\Models\CompanyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash; 
use Carbon\Carbon;

class CompanyManagerController extends Controller
{
    /**
     * Listagem Inicial
     */
    public function index()
    {
        $companies = MainCompany::withCount('users')->get();
        return view('superadmin.companies.index', compact('companies'));
    }

    /**
     * Criar Nova Empresa e Usuário Master
     */
    public function store(Request $request)
    {
        // 1. Limpeza e Validação do CNPJ
        $cnpjLimpo = preg_replace('/\D/', '', $request->cnpj);
        $request->merge(['cnpj' => $cnpjLimpo]);

        $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|unique:main_companies,cnpj',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'cnpj.unique' => 'Este CNPJ já está cadastrado.'
        ]);

        // 2. Criar a Empresa
        $company = new MainCompany();
        $company->name = $request->name;
        $company->cnpj = $cnpjLimpo;
        $company->is_active = true;

        if ($request->hasFile('logo')) {
            $company->logo = $request->file('logo')->store('logos', 'public');
        }

        // Define módulos iniciais da empresa
        $company->mod_lab = $request->has('module_lab');
        $company->mod_stock = $request->has('module_stock');
        $company->mod_roast = $request->has('module_roast');
        $company->mod_prod = $request->has('module_production');
        $company->mod_logistics = $request->has('module_logistics'); // <--- ADICIONADO
        $company->save();

        // 3. Criar o Perfil "Desenvolvedor" para esta Empresa
        $profile = \App\Models\Profile::firstOrCreate(
            ['main_company_id' => $company->id, 'name' => 'Desenvolvedor'],
            [
                'permissions' => [
                    'dev_access' => true,
                    'all_access' => true,
                    'lab_full' => true,
                    'green_coffee_full' => true,
                    'roast_full' => true,
                    'production_full' => true,
                    'logistics_full' => true, // <--- ADICIONADO
                    'settings_edit' => true
                ]
            ]
        );

        // 4. Criar o Usuário Master
        \App\Models\User::firstOrCreate(
            ['username' => 'zevagner', 'main_company_id' => $company->id],
            [
                'profile_id' => $profile->id,
                'name' => 'José Vagner',
                'password' => \Illuminate\Support\Facades\Hash::make('1572'),
                'is_active' => true
            ]
        );

        return back()->with('success', "Empresa, Perfil Master e Usuário configurados com sucesso!");
    }

    /**
     * Retornar JSON para o Modal de Visualização/Edição
     */
    public function getCompanyJson($id)
    {
        try {
            $company = MainCompany::with(['payments' => function($query) {
                $query->latest('payment_date')->take(10); 
            }])->withCount('users')->findOrFail($id);
            
            $modulesActive = [];
            if ($company->mod_lab) $modulesActive[] = 'lab';
            if ($company->mod_stock) $modulesActive[] = 'stock';
            if ($company->mod_roast) $modulesActive[] = 'roast';
            if ($company->mod_prod) $modulesActive[] = 'production';
            if ($company->mod_logistics) $modulesActive[] = 'logistics'; // <--- ADICIONADO

            return response()->json([
                'id' => $company->id,
                'name' => $company->name,
                'cnpj' => preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $company->cnpj),
                'logo' => $company->logo,
                'created_at' => $company->created_at->addHours(3)->format('d/m/Y H:i:s'),
                'modules_active' => $modulesActive,
                'payments' => $company->payments->map(function($p) {
                    return [
                        'date' => \Carbon\Carbon::parse($p->payment_date)->addHours(3)->format('d/m/Y H:i:s'),
                        'amount' => number_format($p->amount, 2, ',', '.')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar dados.'], 500);
        }
    }

    /**
     * Atualizar Dados da Empresa (via Modal View/Edit)
     */
    public function update(Request $request, $id)
    {
        $company = MainCompany::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $company->name = $request->name;
        
        // Módulos
        $company->mod_lab = $request->has('module_lab');
        $company->mod_stock = $request->has('module_stock');
        $company->mod_roast = $request->has('module_roast');
        $company->mod_prod = $request->has('module_production');
        $company->mod_logistics = $request->has('module_logistics'); // <--- ADICIONADO

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                \Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('logos', 'public');
        }

        $company->save();

        return back()->with('success', "Empresa {$company->name} atualizada!");
    }

    /**
     * Registrar Pagamento
     */
    public function registerPayment(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $company = MainCompany::findOrFail($id);

        $duplicate = CompanyPayment::where('main_company_id', $company->id)
            ->where('amount', $request->amount)
            ->where('payment_date', $request->payment_date)
            ->where('created_at', '>', now()->subSeconds(10))
            ->first();

        if ($duplicate) {
            return back()->with('error', 'Este pagamento já foi registrado.');
        }

        CompanyPayment::create([
            'main_company_id' => $company->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        $company->last_payment_date = $request->payment_date;
        $company->save();

        return back()->with('success', "Pagamento de R$ " . number_format($request->amount, 2, ',', '.') . " registrado!");
    }

    /**
     * Ativar/Desativar Empresa
     */
    public function toggleStatus($id)
    {
        $company = MainCompany::findOrFail($id);
        $company->is_active = !$company->is_active;
        $company->save();

        if (!$company->is_active && auth()->user()->main_company_id == $company->id) {
            auth()->guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('identify')->withErrors([
                'cnpj' => 'O acesso desta unidade foi suspenso. Procure a administração.'
            ]);
        }

        $status = $company->is_active ? 'ativada' : 'desativada';
        return back()->with('success', "A empresa {$company->name} foi {$status} com sucesso!");
    }
}