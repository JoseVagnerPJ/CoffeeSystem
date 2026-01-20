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
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define seus modelos de rota, filtros de padrão, etc.
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Rotas de API (Geralmente para Mobile ou integrações externas)
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Rotas WEB (Seu Painel Master deve estar aqui)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configura os limitadores de taxa (Rate Limit) para a API.
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // Compatível com PHP 7.4
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}