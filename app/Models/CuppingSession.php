<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuppingSession extends Model
{
    // Campos que podem ser preenchidos via API/Formulário
    protected $fillable = [
        'title', 
        'session_date', 
        'created_by', 
        'status'
    ];

    // Relacionamento: Uma mesa tem várias posições (itens)
    public function items()
    {
        return $this->hasMany(CuppingItem::class);
    }
}