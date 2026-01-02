<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotAttachment extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa.
     * ADICIONADOS: green_coffee_lot_id, file_path e file_name.
     */
protected $fillable = ['green_coffee_lot_id', 'file_path', 'file_name'];
    /**
     * Relacionamento inverso: Um anexo pertence a um Lote de Café Verde.
     */
    public function greenCoffeeLot(): BelongsTo
    {
        return $this->belongsTo(GreenCoffeeLot::class);
    }
}