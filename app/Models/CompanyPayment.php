<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPayment extends Model
{
    protected $fillable = [
        'main_company_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes'
    ];

    public function company()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }
}