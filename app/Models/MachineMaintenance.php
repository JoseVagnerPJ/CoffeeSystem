<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id', 
        'maintenance_date', 
        'provider', 
        'description', 
        'next_maintenance_date'
    ];

    /**
     * Relacionamento: Uma manutenção pertence a uma máquina.
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}