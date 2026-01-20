<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToCompany;

class LotAttachment extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * Atributos que podem ser preenchidos em massa.
     * ADICIONADOS: main_company_id, green_coffee_lot_id, file_path e file_name.
     */
    protected $fillable = ['main_company_id', 'green_coffee_lot_id', 'file_path', 'file_name'];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento inverso: Um anexo pertence a um Lote de Café Verde.
     */
    public function greenCoffeeLot(): BelongsTo
    {
        return $this->belongsTo(GreenCoffeeLot::class);
    }
}