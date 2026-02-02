<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToCompany;
use Illuminate\Support\Str;

class CuppingSession extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'cupping_profile_id', // Novo: Vínculo com o perfil de atributos
        'uuid',               // Novo: Identificador único para o QR-Code
        'title', 
        'session_date', 
        'created_by', 
        'status',
        'is_blind',           // Novo: Flag para prova às cegas
        'items_count',
        'notes',
        'started_at',
        'finished_at'
    ];

    /**
     * Casts para garantir tipos de dados corretos na API e Flutter
     */
    protected $casts = [
        'is_blind' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'session_date' => 'date',
    ];

    /**
     * Lógica de inicialização do Model
     */
    protected static function booted()
    {
        // Gera o UUID automaticamente ao criar uma nova sessão
        static::creating(function ($session) {
            $session->uuid = (string) Str::uuid();
        });
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * RELACIONAMENTOS
     */

    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento com o perfil que define quais atributos serão provados
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(CuppingProfile::class, 'cupping_profile_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany 
    {
        return $this->hasMany(CuppingItem::class);
    }

    /**
     * Atalho para chegar nas avaliações através dos itens da mesa
     */
    public function evaluations(): HasManyThrough 
    {
        return $this->hasManyThrough(Cup::class, CuppingItem::class);
    }

    /**
     * Usuários do sistema (Q-Graders) que participam da mesa
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cupping_session_user')
                    ->withPivot('has_finished')
                    ->withTimestamps();
    }

    /**
     * Visitantes temporários que entram via QR-Code (Lógica de Campeonatos)
     */
    public function visitors(): HasMany
    {
        return $this->hasMany(CuppingVisitor::class, 'cupping_session_id');
    }
}