<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $fillable = ['green_coffee_lot_id', 'location_id', 'user_id', 'type', 'weight', 'reason', 'notes'];

    public function location() {
        return $this->belongsTo(GreenCoffeeLotLocation::class, 'location_id');
    }

    public function lote()
{
    // Aqui usamos 'lote' como apelido para 'green_coffee_lot_id'
    return $this->belongsTo(GreenCoffeeLot::class, 'green_coffee_lot_id');
}

}