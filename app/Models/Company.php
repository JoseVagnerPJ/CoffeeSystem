<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    public function farms()
{
    return $this->belongsToMany(Farm::class, 'origin_links');
}

    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'contact',
        'action_type',
        'is_international',
        'city',
        'state',
        'country'
    ];
}