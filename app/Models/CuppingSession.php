<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CuppingSession extends Model
{
    protected $fillable = [
        'main_company_id',
        'title', 
        'session_date', 
        'created_by', 
        'status',
        'items_count',
        'notes',
        'started_at',
        'finished_at'
    ];

    // Cast para garantir que o Laravel trate como data/hora ao retornar para o Flutter
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'session_date' => 'date',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany 
    {
        return $this->hasMany(CuppingItem::class);
        
    }

    // Para contar os provadores, precisamos chegar nas avaliações através dos itens
    public function evaluations(): HasManyThrough 
    {
        return $this->hasManyThrough(Cup::class, CuppingItem::class);
    }

    public function users() {
    return $this->belongsToMany(User::class, 'cupping_session_user')->withPivot('has_finished');
}
}