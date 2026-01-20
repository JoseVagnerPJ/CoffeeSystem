<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sample extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id', 'name', 'receipt_date', 'supplier_batch', 'variety', 
        'drying_process', 'fermentation', 'humidity', 
        'screen', 'altitude', 'crop', 'observations',
        'origin_id', 'origin_type' // Adicionados para permitir o salvamento em massa
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function origin()
    {
        return $this->morphTo();
    }

    /**
     * Relacionamento: Uma amostra pode estar presente em vários componentes de perfis sensoriais.
     */
    public function sensoryProfileComponents()
    {
        return $this->hasMany(SensoryProfileComponent::class);
    }
}