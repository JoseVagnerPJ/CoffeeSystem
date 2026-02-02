<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class LogisticRule extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'term',
        'action',
        'fixed_value',
        'target_field'
    ];

    protected $casts = [
        'fixed_value' => 'float',
    ];
}