<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GreenCoffeeLotLocation extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     * Devem corresponder aos campos enviados pelo Controller.
     */
    protected $fillable = [
        'green_coffee_lot_id', // Chave estrangeira
        'address_row',         // Fila (A-F)
        'address_pos',         // Posição (Nº)
        'quantity_bags',       // Sacas inteiras
        'remainder_weight'     // Sobra em kg
    ];

    /**
     * Relacionamento reverso: uma localização pertence a um lote.
     */
    public function lot()
    {
        return $this->belongsTo(GreenCoffeeLot::class, 'green_coffee_lot_id');
    }
}