<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensoryProfile extends Model
{
    protected $fillable = [
        'name', 'is_blend', 'roast_intensity', 'color_hex', 
        'notes', 'sweetness', 'acidity', 'body', 'bitterness'
    ];

    // Relacionamento com os componentes (as amostras que formam o perfil)
    public function components()
    {
        return $this->hasMany(SensoryProfileComponent::class);
    }
    
}