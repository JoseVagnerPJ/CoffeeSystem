<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{

    public function producers()
{
    return $this->belongsToMany(Producer::class, 'origin_links');
}

public function companies()
{
    return $this->belongsToMany(Company::class, 'origin_links');
}
    use HasFactory;

    protected $fillable = [
        'name',
        'altitude',
        'is_international',
        'city',
        'state',
        'country',
        'district',
        'varieties_tags'
    ];

    // Dica: Como varieties_tags será uma lista, 
    // podemos converter automaticamente para array no futuro se precisar.
}