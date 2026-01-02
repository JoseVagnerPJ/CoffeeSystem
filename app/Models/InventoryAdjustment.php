<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'inventory_session_id', 
        'green_coffee_lot_id', 
        'closed_bags',      // Adicione este
        'bag_weight',       // Adicione este
        'remainder_weight', // Adicione este
        'system_weight', 
        'physical_weight', 
        'difference_kg', 
        'reason'
    ];
}
