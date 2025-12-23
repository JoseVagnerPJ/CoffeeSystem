<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * O caminho para a rota "home" da sua aplicação.
     * Após o login, o Laravel redirecionará o usuário para esta rota.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define seus modelos de rota, filtros de padrão, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configura os limitadores de taxa (Rate Limit) para a API.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            /**
             * PHP 7.4: Usamos a função helper optional() 
             * para evitar o erro de sintaxe do operador "?->"
             */
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}