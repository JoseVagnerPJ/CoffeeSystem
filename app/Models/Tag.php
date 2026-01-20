<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['main_company_id', 'name'];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany(): BelongsTo
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function cups(): BelongsToMany
    {
        return $this->belongsToMany(Cup::class, 'cup_tag');
    }
}