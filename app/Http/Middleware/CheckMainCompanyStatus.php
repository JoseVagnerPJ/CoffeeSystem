<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMainCompanyStatus
{
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && !auth()->user()->mainCompany->is_active) {
        

        if ($request->expectsJson()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Empresa inativa.'], 403);
        }

        auth()->guard('web')->logout();
        $request->session()->invalidate();
        
        return redirect()->route('identify')->withErrors(['cnpj' => 'Acesso suspenso para esta unidade.']);
    }

    return $next($request);
}
}