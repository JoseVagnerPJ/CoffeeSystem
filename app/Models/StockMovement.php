<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model 
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id', 
        'green_coffee_lot_id', 
        'location_id', 
        'user_id', 
        'type', 
        'weight', 
        'reason', 
        'notes'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function location() {
        return $this->belongsTo(GreenCoffeeLotLocation::class, 'location_id');
    }

    public function lote()
    {
        // Aqui usamos 'lote' como apelido para 'green_coffee_lot_id'
        return $this->belongsTo(GreenCoffeeLot::class, 'green_coffee_lot_id');
    }

}