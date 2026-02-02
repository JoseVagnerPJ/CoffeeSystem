<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Carrier extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'name',
        'omie_name',
        'api_type',
        'api_token',
        'api_user',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}