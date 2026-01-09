<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Se a requisição espera JSON (como o App Flutter), não redireciona para login
        if ($request->expectsJson()) {
            return null;
        }

        // Se a rota atual for a de política de privacidade, não redirecionamos para o login
        if ($request->is('politica-de-privacidade')) {
            return null;
        }

        return route('login');
    }
}