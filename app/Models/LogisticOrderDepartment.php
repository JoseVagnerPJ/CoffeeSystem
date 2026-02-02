<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticOrderDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'logistic_order_id',
        'department_code',
        'percentage',
        'value',
    ];

    protected $casts = [
        'percentage' => 'float',
        'value'      => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(LogisticOrder::class);
    }
}