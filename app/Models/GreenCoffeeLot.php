<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GreenCoffeeLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sensory_profile_id', 'origin_id', 'origin_type',
        'reference', 'origin', 'process', 'address_row', 'address_pos',
        'quantity_bags', 'unit_weight', 'total_weight',
    ];

    // Carregamento automático de relações para evitar que a View fique vazia
    protected $with = ['attachments', 'sensoryProfile', 'roastingBatches', 'locations'];

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

    public function roastingBatches(): HasMany
    {
        return $this->hasMany(RoastingBatch::class, 'green_coffee_lot_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(GreenCoffeeLotLocation::class);
    }
}

