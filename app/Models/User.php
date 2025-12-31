<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'main_company_id', 'profile_id', 'name', 'username', 'password', 'is_active', 'photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relacionamento com a Empresa Dona do sistema
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    // Relacionamento com o Perfil de Permissões
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function hasPermission($permission)
    {
        // Se o perfil do usuário for nulo, ele não tem permissão
        if (!$this->profile) {
            return false;
        }

        $permissions = $this->profile->permissions;

        // Se o usuário tiver "Acesso Total", ele passa em qualquer teste
        if (isset($permissions['all_access']) && $permissions['all_access']) {
            return true;
        }

        // Verifica se a permissão específica existe e é verdadeira
        return isset($permissions[$permission]) && $permissions[$permission];
    }
}