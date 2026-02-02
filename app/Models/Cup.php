<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cup extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'cupping_item_id', 
        'user_id', 
        
        // Dados de Visitante
        'cupping_visitor_id',
        'visitor_name',
        'visitor_cpf',

        // Atributos SCA (Notas 0 a 10)
        'fragrance_aroma', 
        'flavor', 
        'aftertaste', 
        'acidity', 
        'body', 
        'balance', 
        'uniformity', 
        'clean_cup', 
        'sweetness', 
        'overall',

        // Intensidades e Sub-atributos (Réguas da ficha)
        'roast_level',
        'fragrance_dry',
        'fragrance_break',
        'acidity_intensity',
        'body_level',

        // Defeitos
        'defect_cups',
        'defect_intensity',
        'defects',

        // Totais e Status
        'final_score', 
        'is_invalid', 
        'invalid_reason', 
        'observations'
    ];

    /**
     * Garantia de precisão numérica.
     */
    protected $casts = [
        'fragrance_aroma' => 'float',
        'flavor'          => 'float',
        'aftertaste'       => 'float',
        'acidity'          => 'float',
        'body'             => 'float',
        'balance'          => 'float',
        'uniformity'       => 'float',
        'clean_cup'        => 'float',
        'sweetness'        => 'float',
        'overall'          => 'float',
        'defects'          => 'float',
        'final_score'      => 'float',
        'body_level'        => 'float',
        'acidity_intensity' => 'float', 
        'fragrance_dry'     => 'float', 
        'fragrance_break'   => 'float', 
        'is_invalid'       => 'boolean',
        'roast_level'      => 'integer',
        'defect_cups'      => 'integer',
        'defect_intensity' => 'integer',
    ];

    /**
     * Lógica de cálculo automático do protocolo SCA.
     * Total = (Soma dos 10 atributos positivos) - Defeitos.
     */
protected static function booted()
{
    static::saving(function ($cup) {
        if (!$cup->is_invalid) {
            // 1. Busca o perfil de prova vinculado a esta avaliação
            // Caminho: Cup -> Item -> Session -> Profile
            $profile = $cup->item->session->profile;

            if ($profile) {
                // Lista de atributos e suas chaves no perfil vs chaves na model Cup
                $map = [
                    'eval_fragrance' => 'fragrance_aroma',
                    'eval_flavor'    => 'flavor',
                    'eval_aftertaste'=> 'aftertaste',
                    'eval_acidity'   => 'acidity',
                    'eval_body'      => 'body',
                    'eval_balance'   => 'balance',
                    'eval_uniformity'=> 'uniformity',
                    'eval_clean_cup' => 'clean_cup',
                    'eval_sweetness' => 'sweetness',
                    'eval_overall'   => 'overall',
                ];

                foreach ($map as $profileKey => $cupKey) {
                    // Se o atributo estiver DESATIVADO (0 ou false) no perfil, forçamos nota 10
                    if (!$profile->$profileKey) {
                        $cup->$cupKey = 10.0;
                    }
                }
            }

            // 2. Cálculo de Defeitos
            $cup->defects = (float)($cup->defect_cups * $cup->defect_intensity);

            // 3. Soma dos 10 Atributos (os desativados já valem 10 aqui)
            $positives = (float)$cup->fragrance_aroma + (float)$cup->flavor + 
                         (float)$cup->aftertaste + (float)$cup->acidity + 
                         (float)$cup->body + (float)$cup->balance + 
                         (float)$cup->uniformity + (float)$cup->clean_cup + 
                         (float)$cup->sweetness + (float)$cup->overall;
            
            // 4. Nota Final
            $cup->final_score = round($positives - $cup->defects, 2);
        } else {
            $cup->final_score = 0;
        }
    });
}

    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CuppingItem::class, 'cupping_item_id');
    }

    /**
     * Provador (Nullable para permitir visitantes via QR-Code)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Notas Sensoriais (Tags)
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'cup_tag')
                    ->withPivot('main_company_id');
    }

    
}