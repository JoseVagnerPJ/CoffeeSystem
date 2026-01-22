<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMainCompanyStatus
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Verifica se o usuário está logado e se a relação existe
        if (auth()->check() && auth()->user()->mainCompany) {
            
            // Se a empresa estiver INATIVA
            if (!auth()->user()->mainCompany->is_active) {

                // COMPORTAMENTO PARA API (FLUTTER)
                if ($request->is('api/*') || $request->expectsJson()) {
                    // Tenta deletar o token apenas se ele existir
                    $token = $request->user()->currentAccessToken();
                    if ($token) {
                        $token->delete();
                    }
                    return response()->json(['message' => 'O acesso desta unidade está suspenso.'], 403);
                }

                // COMPORTAMENTO PARA WEB (NAVEGADOR)
                auth()->guard('web')->logout();
                
                // Só tenta mexer na sessão se ela estiver disponível
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
                
                return redirect()->route('identify')->withErrors(['cnpj' => 'Acesso suspenso para esta unidade.']);
            }
        }

        return $next($request);
    }
}