<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineCleaningLog extends Model
{
    protected $fillable = ['machine_id', 'user_id', 'cleaned_at', 'notes'];

    protected $casts = [
        'cleaned_at' => 'datetime',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}