<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('main_company_id', function (Builder $builder) {
            
            if (app()->runningInConsole()) {
                return;
            }

            $companyId = null;

            // 1. Verifica se existe sessão (Contexto Web)
            // O request()->hasSession() evita o erro 500 na API
            if (request()->hasSession() && session()->has('login_company_id')) {
                $companyId = session('login_company_id');
            } 
            
            // 2. Verifica se há usuário autenticado (Contexto API/Sanctum ou Web logado)
            elseif (auth()->check()) {
                $companyId = auth()->user()->main_company_id;
            }

            // 3. Aplica o filtro apenas se encontrar um ID
            if ($companyId) {
                $tableName = $builder->getModel()->getTable();
                $builder->where($tableName . '.main_company_id', $companyId);
            }
        });

        static::creating(function ($model) {
            $companyId = null;

            if (request()->hasSession()) {
                $companyId = session('login_company_id');
            }

            if (!$companyId && auth()->check()) {
                $companyId = auth()->user()->main_company_id;
            }
            
            if ($companyId && !$model->main_company_id) {
                $model->main_company_id = $companyId;
            }
        });
    }
}