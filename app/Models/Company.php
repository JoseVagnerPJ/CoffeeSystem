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

    public function producers() 
{
    return $this->belongsToMany(Producer::class, 'origin_links', 'company_id', 'producer_id');
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