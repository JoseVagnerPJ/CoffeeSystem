<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Profile extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * Atributos que podem ser preenchidos em massa (Mass Assignment).
     * Essencial para que o Profile::create() funcione com esses campos.
     */
    protected $fillable = [
        'main_company_id', 
        'name', 
        'permissions'
    ];

    /**
     * Casts: Converte automaticamente o campo JSON de permissões 
     * do banco de dados em um Array PHP e vice-versa.
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Relacionamento: O perfil pertence a uma empresa principal (Tenant).
     * Isso garante que cada empresa tenha seus próprios níveis de acesso.
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento: Um perfil pode ser atribuído a vários usuários. Ex: O perfil "Mestre de Torra" pode estar em 3 colaboradores diferentes.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'profile_id');
    }
}