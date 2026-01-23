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
        'acidity', 
        'body', 
        'sweetness', 
        'aftertaste', 
        'final_score', 
        'is_invalid', 
        'invalid_reason', 
        'observations'
    ];

    /**
     * Garantia de precisão numérica para o Flutter e para o Banco.
     */
    protected $casts = [
        'acidity'     => 'float',
        'body'        => 'float',
        'sweetness'   => 'float',
        'aftertaste'  => 'float',
        'final_score' => 'float',
        'is_invalid'  => 'boolean',
    ];

    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Lógica de cálculo automático da nota final.
     * $$FinalScore = 60 + Acidez + Corpo + Doçura + Retrogosto$$
     */
    protected static function booted()
    {
        static::saving(function ($cup) {
            if (!$cup->is_invalid) {
                // Somamos os valores e arredondamos para 2 casas decimais
                $total = 60 + 
                    (float)$cup->acidity + 
                    (float)$cup->body + 
                    (float)$cup->sweetness + 
                    (float)$cup->aftertaste;
                
                $cup->final_score = round($total, 2);
            } else {
                $cup->final_score = 0;
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CuppingItem::class, 'cupping_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento Many-to-Many configurado para Multi-empresa.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'cup_tag')
                    ->withPivot('main_company_id'); // Essencial para rastrear a empresa na tabela pivô
    }
}