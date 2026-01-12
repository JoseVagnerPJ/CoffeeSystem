<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class GreenCoffeeLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sensory_profile_id', 'origin_id', 'origin_type',
        'reference', 'origin', 'process', 'address_row', 'address_pos',
        'quantity_bags', 'unit_weight', 'total_weight',
    ];

    // Carregamento automático de relações para evitar que a View fique vazia
    // Nota: Removi 'roastingBatches' daqui para evitar que o erro trave a página 
    // enquanto o banco de dados processa a relação complexa.
    protected $with = ['attachments', 'sensoryProfile', 'locations'];

    public function attachments(): HasMany
    {
        return $this->hasMany(LotAttachment::class, 'green_coffee_lot_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sensoryProfile(): BelongsTo
    {
        return $this->belongsTo(SensoryProfile::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo('origin', 'origin_type', 'origin_id');
    }

    /**
     * Relacionamento Corrigido:
     * Busca as Torras (Batches) ATRAVÉS das Demandas (RoastDemands)
     */
    public function roastingBatches(): HasManyThrough
    {
        return $this->hasManyThrough(
            RoastingBatch::class, // Modelo Final
            RoastDemand::class,   // Modelo Intermediário (a ponte)
            'green_coffee_lot_id', // Chave estrangeira em RoastDemand (aponta para este lote)
            'roast_demand_id',     // Chave estrangeira em RoastingBatch (aponta para a demanda)
            'id',                  // Chave local em GreenCoffeeLot
            'id'                   // Chave local em RoastDemand
        );
    }

    public function locations(): HasMany
    {
        return $this->hasMany(GreenCoffeeLotLocation::class);
    }

    // Dentro do arquivo GreenCoffeeLot.php
public function roastDemands()
{
    return $this->hasMany(RoastDemand::class, 'green_coffee_lot_id');
}
}