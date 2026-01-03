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

        // Limpa o CNPJ vindo do celular para garantir que são apenas números
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $request->cnpj);

        // BUSCA CORRIGIDA: Nome do relacionamento deve ser 'mainCompany'
        $user = User::where('username', $request->username)
            ->whereHas('mainCompany', function ($query) use ($cnpjLimpo) {
                // Remove formatação do banco em tempo real para comparar com o CNPJ limpo
                $query->whereRaw("REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', '') = ?", [$cnpjLimpo]);
            })->first();

        // Verifica se o usuário existe e se a senha está correta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['As credenciais ou o CNPJ informado estão incorretos.'],
            ]);
        }

        // Gera o token e retorna para o celular
        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                // Corrigido para main_company_id conforme seu Model
                'company_id' => $user->main_company_id 
            ]
        ]);
    }
}