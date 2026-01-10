<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'name', 
        'brand', 
        'model', 
        'year', 
        'serial_number', 
        'max_capacity', 
        'min_capacity', 
        'cleaning_threshold', 
        'current_roasts', 
        'is_active'
    ];

    /**
     * Relacionamento: Uma máquina tem muitas manutenções.
     */
    public function maintenances()
    {
        return $this->hasMany(MachineMaintenance::class)->orderBy('maintenance_date', 'desc');
    }

    public function getCleaningStatusAttribute()
{
    $remaining = $this->cleaning_threshold - $this->current_roasts;

    if ($remaining <= 0) return 'critical'; // Passou do limite
    if ($remaining <= 5) return 'warning';  // Alerta inicial
    return 'ok';
}
}