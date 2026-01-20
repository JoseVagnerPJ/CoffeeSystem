<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class MachineMaintenance extends Model
{

    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id',
        'machine_id', 
        'maintenance_date', 
        'provider', 
        'description', 
        'next_maintenance_date'
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    /**
     * Relacionamento: Uma manutenção pertence a uma máquina.
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}