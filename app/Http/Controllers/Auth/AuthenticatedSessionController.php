<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\MainCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * PASSO 1: Exibe a tela de identificação por CNPJ.
     */
    public function createIdentify()
    {
        return view('auth.identify');
    }

    /**
     * PASSO 1: Valida o CNPJ e salva os dados da empresa na sessão.
     */
    public function storeIdentify(Request $request)
    {
        $request->validate([
            'cnpj' => 'required'
        ]);
        
        $cnpjLimpo = preg_replace('/\D/', '', $request->cnpj);
        
        $company = MainCompany::where('cnpj', $cnpjLimpo)->first();

        if (!$company) {
            return back()->withErrors(['cnpj' => 'Empresa não encontrada com este CNPJ.']);
        }

        // --- AJUSTE: Verifica se a empresa está ativa antes de prosseguir ---
        if (!$company->is_active) {
            return back()->withErrors(['cnpj' => 'O acesso para esta empresa está suspenso. Entre em contato com o suporte.']);
        }

        session([
            'login_company_id'   => $company->id,
            'login_company_name' => $company->name,
            'login_company_logo' => $company->logo
        ]);

        return redirect()->route('login');
    }

    /**
     * PASSO 2: Exibe a tela de login (Usuário e Senha).
     */
    public function create()
    {
        if (!session()->has('login_company_id')) {
            return redirect()->route('identify');
        }
        return view('auth.login');
    }

    /**
     * PASSO 2: Processa a autenticação final.
     */
    public function store(LoginRequest $request)
    {
        // 1. Tenta autenticar as credenciais (username/password)
        $request->authenticate();

        // --- AJUSTE: Verifica se o usuário logado pertence à empresa da sessão ---
        if (Auth::user()->main_company_id !== session('login_company_id')) {
            Auth::logout();
            
            // Limpa a sessão para obrigar a re-identificação
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'username' => 'Este usuário não possui permissão para acessar esta unidade.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Finaliza a sessão do usuário.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}