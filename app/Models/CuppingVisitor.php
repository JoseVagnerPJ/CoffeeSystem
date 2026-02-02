<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuppingVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'cupping_session_id',
        'name',
        'cpf'
    ];

    /**
     * Relacionamento: O visitante pertence a uma sessão (mesa) de prova específica.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(CuppingSession::class, 'cupping_session_id');
    }

    /**
     * Relacionamento: Um visitante pode ter várias avaliações (uma para cada xícara da mesa).
     */
    public function evaluations(): HasMany
    {
        // Aqui usamos o campo visitor_cpf ou criaremos um visitor_id na tabela 'cups'
        // Para simplificar a Fase 1, as avaliações serão vinculadas via CPF no campo que criamos na tabela 'cups'
        return $this->hasMany(Cup::class, 'visitor_cpf', 'cpf');
    }
}