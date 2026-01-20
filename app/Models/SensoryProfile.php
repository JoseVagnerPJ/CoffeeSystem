<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensoryProfile extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'name', 'is_blend', 'roast_intensity', 'color_hex', 
        'notes', 'sweetness', 'acidity', 'body', 'bitterness'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    // Relacionamento com os componentes (as amostras que formam o perfil)
    public function components()
    {
        return $this->hasMany(SensoryProfileComponent::class);
    }
    
}