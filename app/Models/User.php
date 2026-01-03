<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'main_company_id', 'profile_id', 'name', 'username', 'password', 'is_active', 'photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relacionamento com a Empresa Dona do sistema
    public function mainCompany()
    {
        // Aqui o nome é mainCompany, deve ser o mesmo no Controller
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    // Relacionamento com o Perfil de Permissões
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function hasPermission($permission)
    {
        if (!$this->profile) {
            return false;
        }

        $permissions = $this->profile->permissions;

        if (isset($permissions['all_access']) && $permissions['all_access']) {
            return true;
        }

        return isset($permissions[$permission]) && $permissions[$permission];
    }
}