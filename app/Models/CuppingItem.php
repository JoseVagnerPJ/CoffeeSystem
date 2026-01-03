<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuppingItem extends Model
{
    protected $fillable = [
        'cupping_session_id', 
        'sample_id', 
        'position'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Cup::class);
    }
}