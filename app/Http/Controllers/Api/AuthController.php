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
        // 1. Validação dos dados que chegam do Flutter
        $request->validate([
            'cnpj'        => 'required',
            'username'    => 'required',
            'password'    => 'required',
            'device_name' => 'required', 
        ]);

        // 2. Limpeza do CNPJ (Apenas números)
        $cnpjLimpo = preg_replace('/[^0-9]/', '', $request->cnpj);

        // 3. Busca a empresa ignorando formatação no banco
        $company = MainCompany::whereRaw("REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', '') = ?", [$cnpjLimpo])->first();

        if (!$company) {
            return response()->json(['message' => 'Empresa não encontrada com este CNPJ.'], 401);
        }

        if (!$company->is_active) {
            return response()->json(['message' => 'O acesso desta unidade está suspenso.'], 403);
        }

        // 4. Busca o usuário (Ignora escopos globais de empresa pois ainda não estamos logados)
        $user = User::withoutGlobalScopes()
            ->where('username', $request->username)
            ->where('main_company_id', $company->id)
            ->where('is_active', true)
            ->first();

        // 5. Validação de senha
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Usuário ou senha incorretos para esta unidade.'], 401);
        }

        // 6. Verificação de permissão para o módulo de Laboratório (Q-App)
        if (!$user->hasPermission('lab_view') && !$user->hasPermission('lab_full')) {
            return response()->json(['message' => 'Seu perfil não tem permissão para usar este App.'], 403); 
        }

        // 7. Geração do Token via Sanctum
        try {
        // Geramos uma string aleatória para o token
        $plainTextToken = \Illuminate\Support\Str::random(40);
        
        // Usamos o Query Builder (DB) para inserir direto na tabela. 
        // Isso ignora proteções de model e permite passar o ID obrigatório.
        $tokenId = \Illuminate\Support\Facades\DB::table('personal_access_tokens')->insertGetId([
            'main_company_id' => $user->main_company_id, // O campo que estava faltando
            'tokenable_type'  => get_class($user),
            'tokenable_id'    => $user->id,
            'name'            => $request->device_name,
            'token'           => hash('sha256', $plainTextToken),
            'abilities'       => '["*"]', // Permissão total padrão
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // O Flutter espera o token no formato "ID|Texto"
        $token = $tokenId . '|' . $plainTextToken;

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro interno ao gerar acesso.',
            'error'   => $e->getMessage()
        ], 500);
    }

        // 8. Retorno esperado pelo Flutter
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
