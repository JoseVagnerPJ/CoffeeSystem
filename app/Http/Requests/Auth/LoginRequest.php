<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        // Pegamos o ID da empresa que foi salvo na sessão no Passo 1 (CNPJ)
        $companyId = session('login_company_id');

        $credentials = [
            'username'        => $this->input('username'),
            'password'        => $this->input('password'),
            'main_company_id' => $companyId,
            'is_active'       => 1
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('Usuário ou senha inválidos para esta empresa.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) { return; }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    public function throttleKey()
    {
        return Str::lower($this->input('username')).'|'.session('login_company_id').'|'.$this->ip();
    }
}