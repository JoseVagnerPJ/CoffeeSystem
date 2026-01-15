<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
{
    $this->registerPolicies();

    // Registra um Gate genérico que verifica as permissões do banco de dados
    // Isso faz o ->middleware('can:chave') funcionar automaticamente
    \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
        if ($user->hasPermission($ability)) {
            return true;
        }
    });
}
}
