<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        'name', 'receipt_date', 'supplier_batch', 'variety', 
        'drying_process', 'fermentation', 'humidity', 
        'screen', 'altitude', 'crop', 'observations',
        'origin_id', 'origin_type' // Adicionados para permitir o salvamento em massa
    ];

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