<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CuppingItem extends Model
{
protected $fillable = [
    'cupping_session_id', 
    'sample_id', 
    'position',
    'is_avulso',
    'nickname',
    'variety',
    'drying_process',
    'fermentation',
    'humidity',
    'screen',
    'altitude',
    'crop',
    'origin_id',    // Adicione se não houver
    'origin_type',  // Adicione se não houver
    'notes'
];
    public function session(): BelongsTo
    {
        return $this->belongsTo(CuppingSession::class, 'cupping_session_id');
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * Permite que a origem seja um Produtor, Fazenda ou Empresa
     */
    public function origin(): MorphTo
    {
        return $this->morphTo();
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Cup::class);
    }
}