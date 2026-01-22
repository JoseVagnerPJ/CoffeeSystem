<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MainCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
{
    // 1. Validação rápida
    $request->validate([
        'cnpj'        => 'required',
        'username'    => 'required',
        'password'    => 'required',
        'device_name' => 'required', 
    ]);

    $cnpjLimpo = preg_replace('/[^0-9]/', '', $request->cnpj);

// 1. Buscamos a empresa ignorando qualquer formatação
$company = \App\Models\MainCompany::whereRaw("REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', '') = ?", [$cnpjLimpo])->first();

if (!$company) {
    return response()->json(['message' => 'Empresa não encontrada.'], 404);
}

// 2. BUSCA DO USUÁRIO: O pulo do gato está no 'withoutGlobalScopes()'
// Isso impede que a Trait BelongsToCompany tente filtrar antes do login
$user = \App\Models\User::withoutGlobalScopes()
    ->where('username', $request->username)
    ->where('main_company_id', $company->id)
    ->where('is_active', true)
    ->first();

if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
    return response()->json(['message' => 'Credenciais incorretas.'], 401);
}

    // 5. Valida permissão de acesso ao App
    if (!$user->hasPermission('lab_view') && !$user->hasPermission('lab_full')) {
        return response()->json(['message' => 'Seu perfil não tem permissão para usar este App.'], 403); 
    }

    // 6. Geração do Token (Aqui é onde o Erro 500 costuma acontecer se a tabela não existir)
    try {
        $token = $user->createToken($request->device_name)->plainTextToken;
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao gerar token. Execute as migrações no servidor.',
            'error' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'token' => $token,
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