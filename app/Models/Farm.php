<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
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