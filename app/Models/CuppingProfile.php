<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuppingProfile extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'name',
        'description',
        
        // Atributos Principais (Flags de exibição)
        'eval_fragrance',   // Aroma/Fragrância
        'eval_flavor',      // Sabor
        'eval_aftertaste',  // Retrogosto
        'eval_acidity',     // Acidez
        'eval_body',        // Corpo
        'eval_balance',     // Equilíbrio
        'eval_uniformity',  // Uniformidade
        'eval_clean_cup',   // Xícara Limpa
        'eval_sweetness',   // Doçura
        'eval_overall',     // Geral (Nota do Provador)
        'eval_defects',     // Defeitos (O atributo negativo)

        // Sub-Atributos e Detalhes SCA (Para fidelidade total à ficha)
        'show_roast_level',      // Mostrar seleção de nível de torra
        'show_fragrance_scales', // Mostrar escalas de 'Dry' e 'Break' no Aroma
        'show_intensity_scales', // Mostrar escalas de intensidade (Low/High) para Acidez e Corpo
        'show_defect_details',   // Mostrar seletor de # xícaras x intensidade (2 ou 4)
    ];

    /**
     * Casts para garantir que o Laravel trate as flags como booleanos.
     */
    protected $casts = [
        'eval_fragrance'        => 'boolean',
        'eval_flavor'           => 'boolean',
        'eval_aftertaste'       => 'boolean',
        'eval_acidity'          => 'boolean',
        'eval_body'             => 'boolean',
        'eval_balance'          => 'boolean',
        'eval_uniformity'       => 'boolean',
        'eval_clean_cup'        => 'boolean',
        'eval_sweetness'        => 'boolean',
        'eval_overall'          => 'boolean',
        'eval_defects'          => 'boolean',
        'show_roast_level'      => 'boolean',
        'show_fragrance_scales' => 'boolean',
        'show_intensity_scales' => 'boolean',
        'show_defect_details'   => 'boolean',
    ];

    /**
     * Relacionamento: O perfil pertence a uma empresa (Multi-tenant).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }
    
    /**
     * Relacionamento: Um perfil de prova pode estar em várias sessões (mesas).
     */
    public function sessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CuppingSession::class, 'cupping_profile_id');
    }
}