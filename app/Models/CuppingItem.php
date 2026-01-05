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
        'is_avulso',
        'position',
        'variety',
        'roast_type',
        'drying_process',
        'fermentation',
        'humidity',
        'screen',
        'altitude',
        'crop',
        'origin_id',
        'origin_type',
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