<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuppingSession extends Model
{
    protected $fillable = [
        'title', 
        'session_date', 
        'created_by', 
        'status',
        'notes'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CuppingItem::class)->orderBy('position', 'asc');
    }
}