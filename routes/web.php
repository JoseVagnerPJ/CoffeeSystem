<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-db', function () {
    try {
        // Tenta buscar todas as empresas (vai retornar um array vazio se não houver dados)
        $companies = Company::all();
        return response()->json([
            'status' => 'Conectado com sucesso!',
            'tabelas' => DB::select('SHOW TABLES'),
            'dados_companies' => $companies
        ]);
    } catch (\Exception $e) {
        return "Erro ao conectar: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    // Pegamos estatísticas reais do seu banco SQL
    $stats = [
        'empresas' => Company::count(),
        'usuarios' => User::count(),
        'lotes' => DB::table('green_coffee_lots')->count(),
        'maquinas' => DB::table('roasting_machines')->count(),
    ];

    return view('welcome', compact('stats'));
});
