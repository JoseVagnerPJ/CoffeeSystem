<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoastingBatch extends Model
{
    protected $fillable = [
    'roast_demand_id', // O ID correto conforme sua migration
    'mestre_user_id', 
    'batch_number', 
    'weight_in', 
    'weight_out', 
    'status'
];

public function greenCoffeeLot()
{
    return $this->belongsTo(GreenCoffeeLot::class);
}

public function machine()
{
    return $this->belongsTo(Machine::class);
}

public function demand()
{
    return $this->belongsTo(RoastDemand::class, 'roast_demand_id');
}
}
