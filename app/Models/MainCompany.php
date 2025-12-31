<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCompany extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cnpj', 'logo'];

    /**
     * Relacionamento: Uma empresa possui muitos usuários.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'main_company_id');
    }

    /**
     * Relacionamento: Uma empresa possui muitos perfis de permissão.
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class, 'main_company_id');
    }
}