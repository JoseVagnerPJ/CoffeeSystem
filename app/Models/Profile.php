<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['main_company_id', 'name', 'permissions'];

    /**
     * Converte o campo JSON de permissões automaticamente em Array.
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Relacionamento: O perfil pertence a uma empresa principal.
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento: Um perfil pode estar vinculado a vários usuários.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}