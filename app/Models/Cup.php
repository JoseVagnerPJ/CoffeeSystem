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
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Lógica de cálculo automático da nota final: 60 + 4 pilares.
     * $$FinalScore = 60 + Acidez + Corpo + Doçura + Retrogosto$$
     */
    protected static function booted()
    {
        static::saving(function ($cup) {
            if (!$cup->is_invalid) {
                // Soma os valores decimais à base 60
                $cup->final_score = 60 + 
                    (float)$cup->acidity + 
                    (float)$cup->body + 
                    (float)$cup->sweetness + 
                    (float)$cup->aftertaste;
            } else {
                // Se for inválida, a nota é zerada para não afetar médias
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

    // Relacionamento Many-to-Many com a tabela cup_tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'cup_tag');
    }
}