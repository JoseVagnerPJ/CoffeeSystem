<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoastDemand extends Model {
    protected $fillable = ['user_id', 'green_coffee_lot_id', 'machine_id', 'mestre_user_id', 'reference', 'total_weight_requested', 'roast_level', 'status', 'notes', 'is_special_tour'];

    public function lot() { return $this->belongsTo(GreenCoffeeLot::class, 'green_coffee_lot_id'); }
    public function machine() { return $this->belongsTo(Machine::class); }
public function gestor() 
{
    return $this->belongsTo(User::class, 'user_id');
}

public function mestre() 
{
    return $this->belongsTo(User::class, 'mestre_user_id');
}

public function batches() 
{
    return $this->hasMany(RoastingBatch::class);
}
}