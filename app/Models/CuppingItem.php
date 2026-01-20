<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CuppingItem extends Model
{
    use HasFactory, BelongsToCompany;

protected $fillable = [
    'main_company_id',
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
    'origin_id',
    'origin_type',
    'notes'
];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

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