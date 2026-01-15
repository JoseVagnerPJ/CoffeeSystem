<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Lógica Global de Autorização
         * O Gate::before é executado antes de qualquer outra verificação.
         * Ele consulta o método hasPermission($ability) que você definiu no Model User.
         */
        Gate::before(function (User $user, $ability) {
            if ($user->hasPermission($ability)) {
                return true;
            }
        });
    }
}