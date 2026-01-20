<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Farm extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'name',
        'altitude',
        'is_international',
        'city',
        'state',
        'country',
        'district',
        'varieties_tags',
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
     * Relacionamento Polimórfico: Uma fazenda possui muitas amostras.
     */
    public function samples()
    {
        return $this->morphMany(Sample::class, 'origin');
    }

    /**
     * Relacionamento: Produtores vinculados a esta fazenda.
     */
    public function producers()
    {
        return $this->belongsToMany(Producer::class, 'origin_links', 'farm_id', 'producer_id');
    }

    /**
     * Relacionamento: Empresas vinculadas a esta fazenda.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'origin_links', 'farm_id', 'company_id');
    }
}