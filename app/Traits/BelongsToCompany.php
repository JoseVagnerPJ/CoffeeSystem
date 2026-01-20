<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // 1. Escopo Global: Toda consulta filtrará automaticamente
        static::addGlobalScope('main_company_id', function (Builder $builder) {
            
            // Se estiver rodando via terminal (Seeders/Migrations), não aplica o escopo
            if (app()->runningInConsole()) {
                return;
            }

            $companyId = null;

            // Tenta pegar o ID da empresa pela sessão (mais seguro para evitar recursão no User)
            if (session()->has('login_company_id')) {
                $companyId = session('login_company_id');
            } 
            // Se já estiver logado (em outras partes do sistema), pega do objeto User
            elseif (auth()->check()) {
                $companyId = auth()->user()->main_company_id;
            }

            if ($companyId) {
                $tableName = $builder->getModel()->getTable();
                $builder->where($tableName . '.main_company_id', $companyId);
            }
        });

        // 2. Evento de Criação: Injeta o ID automaticamente ao salvar
        static::creating(function ($model) {
            $companyId = session('login_company_id') ?? (auth()->check() ? auth()->user()->main_company_id : null);
            
            if ($companyId && !$model->main_company_id) {
                $model->main_company_id = $companyId;
            }
        });
    }
}