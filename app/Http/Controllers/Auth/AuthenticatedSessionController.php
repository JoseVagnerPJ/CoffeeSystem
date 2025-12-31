<?php

namespace App\Http\Controllers\Auth; // Namespace corrigido para a pasta Auth

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\MainCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Limpa a pontuação do CNPJ antes de buscar no banco
        $cnpjLimpo = preg_replace('/\D/', '', $request->cnpj);
        
        $company = MainCompany::where('cnpj', $cnpjLimpo)->first();

        if (!$company) {
            return back()->withErrors(['cnpj' => 'Empresa não encontrada com este CNPJ.']);
        }

        // Armazena dados da empresa na sessão para o Passo 2
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
        // Se não houver empresa na sessão, volta para a identificação
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
        $request->authenticate();

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