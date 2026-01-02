<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoastingBatch extends Model
{
    protected $fillable = ['green_coffee_lot_id', 'machine_id', 'input_weight', 'output_weight', 'status'];

public function greenCoffeeLot()
{
    return $this->belongsTo(GreenCoffeeLot::class);
}

public function machine()
{
    return $this->belongsTo(Machine::class);
}
}
