<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoastDemand extends Model 
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'user_id', 
        'green_coffee_lot_id', 
        'sensory_profile_id', 
        'machine_id', 
        'mestre_user_id', 
        'reference', 
        'total_weight_requested', 
        'partial_weight',
        'roast_level', 
        'status', 
        'notes',
        'position'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function lot(): BelongsTo 
    { 
        return $this->belongsTo(GreenCoffeeLot::class, 'green_coffee_lot_id'); 
    }

    public function sensoryProfile(): BelongsTo 
    { 
        return $this->belongsTo(SensoryProfile::class, 'sensory_profile_id'); 
    }

    public function machine(): BelongsTo 
    { 
        return $this->belongsTo(Machine::class); 
    }

    public function gestor(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mestre(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'mestre_user_id');
    }

    public function batches(): HasMany 
    {
        return $this->hasMany(RoastingBatch::class);
    }
}