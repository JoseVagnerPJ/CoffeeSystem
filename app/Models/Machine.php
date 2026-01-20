<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Machine extends Model
{
    use HasFactory, BelongsToCompany;

    // Campos que podem ser preenchidos em massa
protected $fillable = [
    'main_company_id', 
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
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento: Uma máquina tem muitas manutenções.
     */
    public function maintenances()
    {
        return $this->hasMany(MachineMaintenance::class)->orderBy('maintenance_date', 'desc');
    }

    public function cleaningLogs()
{
    return $this->hasMany(MachineCleaningLog::class);
}

    public function getCleaningStatusAttribute()
{
    $remaining = $this->cleaning_threshold - $this->current_roasts;

    if ($remaining <= 0) return 'critical'; // Passou do limite
    if ($remaining <= 5) return 'warning';  // Alerta inicial
    return 'ok';
}
}