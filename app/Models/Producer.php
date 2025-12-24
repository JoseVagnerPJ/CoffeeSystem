<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cpf', 'contact'];

    // ADICIONE ESTA FUNÇÃO:
    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'origin_links', 'producer_id', 'farm_id');
    }
}