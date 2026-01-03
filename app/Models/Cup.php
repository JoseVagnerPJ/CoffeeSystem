<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cup extends Model
{
    protected $fillable = [
        'cupping_item_id', 
        'user_id', 
        'acidity', 
        'body', 
        'sweetness', 
        'aftertaste', 
        'final_score', 
        'is_invalid', 
        'invalid_reason', 
        'observations'
    ];

public function tags()
{
    return $this->belongsToMany(Tag::class);
}
}