<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoastingBatch extends Model
{
    protected $guarded = []; 

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_special_tour' => 'boolean',
    ];

    // Relação com a Demanda (Pai)
    public function demand() {
        return $this->belongsTo(RoastDemand::class, 'roast_demand_id');
    }

    // Relação com o Mestre
    public function mestre() {
        return $this->belongsTo(User::class, 'mestre_user_id');
    }

    /**
     * ATENÇÃO: Esta é a relação que está faltando!
     * Ela busca a máquina através da demanda.
     */
    public function machine()
    {
        return $this->hasOneThrough(
            Machine::class,      // O modelo que queremos chegar
            RoastDemand::class,  // O modelo intermediário
            'id',                // Chave estrangeira em RoastDemand (id da demanda)
            'id',                // Chave estrangeira em Machine (id da máquina)
            'roast_demand_id',   // Chave local em RoastingBatch
            'machine_id'         // Chave local em RoastDemand
        );
    }
}