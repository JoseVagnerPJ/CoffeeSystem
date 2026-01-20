<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Company extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'name',
        'cnpj',
        'contact',
        'action_type',
        'is_international',
        'city',
        'state',
        'country',
        'is_active'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento Polimórfico: Uma empresa possui muitas amostras.
     */
    public function samples()
    {
        return $this->morphMany(Sample::class, 'origin');
    }

    /**
     * Relacionamento: Fazendas vinculadas a esta empresa.
     */
    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'origin_links', 'company_id', 'farm_id');
    }

    /**
     * Relacionamento: Produtores vinculados a esta empresa.
     */
    public function producers() 
    {
        return $this->belongsToMany(Producer::class, 'origin_links', 'company_id', 'producer_id');
    }
}