<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Producer extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['main_company_id', 'name', 'cpf', 'contact', 'is_active'];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento Polimórfico: Um produtor possui muitas amostras.
     */
    public function samples()
    {
        return $this->morphMany(Sample::class, 'origin');
    }

    /**
     * Relacionamento: Fazendas vinculadas a este produtor.
     */
    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'origin_links', 'producer_id', 'farm_id');
    }

    /**
     * Relacionamento: Empresas vinculadas a este produtor.
     */
    public function companies() 
    {
        return $this->belongsToMany(Company::class, 'origin_links', 'producer_id', 'company_id');
    }
}