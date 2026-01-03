<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // Permite salvar o nome da nota sensorial (ex: Chocolate, Frutado)
    protected $fillable = ['name'];

    // Relacionamento inverso: Uma tag pode estar em várias xícaras
    public function cups()
    {
        return $this->belongsToMany(Cup::class);
    }
}