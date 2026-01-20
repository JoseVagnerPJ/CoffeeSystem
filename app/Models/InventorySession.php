<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventorySession extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['main_company_id', 'user_id', 'inventory_date', 'status', 'total_diff_kg', 'notes'];

    protected $casts = [
        'total_diff_kg' => 'decimal:2'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}