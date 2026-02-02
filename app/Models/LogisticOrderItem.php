<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'logistic_order_id',
        'product_code',
        'description',
        'quantity',
        'unit_value',
        'total_value',
    ];

    protected $casts = [
        'quantity'    => 'float',
        'unit_value'  => 'float',
        'total_value' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(LogisticOrder::class);
    }
}