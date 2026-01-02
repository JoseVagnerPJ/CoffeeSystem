<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySession extends Model
{
    protected $fillable = ['user_id', 'inventory_date', 'status', 'total_diff_kg', 'notes'];

    protected $casts = [
        'total_diff_kg' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}