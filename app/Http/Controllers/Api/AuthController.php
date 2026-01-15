<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'cnpj'        => 'required',
            'username'    => 'required',
            'password'    => 'required',
            'device_name' => 'required', 
        ]);

        // 1. Limpa o CNPJ vindo do celular para garantir que são apenas números
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $request->cnpj);

        // 2. Busca o usuário garantindo que ele esteja ativo e pertença à empresa correta
        $user = User::where('username', $request->username)
            ->where('is_active', true) // Garante que o usuário está ativo
            ->whereHas('mainCompany', function ($query) use ($cnpjLimpo) {
                // Busca o CNPJ ignorando qualquer formatação (ponto, barra, traço)
                $query->whereRaw("REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', '') = ?", [$cnpjLimpo]);
            })->first();

        // 3. Verifica se o usuário existe e se a senha está correta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['As credenciais ou o CNPJ informado estão incorretos.'],
            ]);
        }

        // 4. TRAVA DE SEGURANÇA DO APP: Verifica se tem permissão de Laboratório
        // Se não tiver Lab View e nem Lab Full, bloqueia o acesso ao Q-App
        if (!$user->hasPermission('lab_view') && !$user->hasPermission('lab_full')) {
            return response()->json([
                'message' => 'Acesso Negado: Seu perfil não possui permissão para utilizar o módulo de Laboratório.'
            ], 403); 
        }

        // 5. Gera o token e retorna os dados incluindo as permissões para o Flutter
        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            // Informa ao Flutter se ele pode ver o botão de "Nova Mesa"
            'can_create_sessions' => $user->hasPermission('lab_full'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'company_id' => $user->main_company_id 
            ]
        ]);
    }
}