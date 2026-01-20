<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineCleaningLog extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['main_company_id', 'machine_id', 'user_id', 'cleaned_at', 'notes'];

    protected $casts = [
        'cleaned_at' => 'datetime',
    ];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}