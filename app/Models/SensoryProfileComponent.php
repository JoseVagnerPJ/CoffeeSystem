<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensoryProfileComponent extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['main_company_id', 'sensory_profile_id', 'sample_id', 'percentage'];

    /**
     * Relacionamento: Pertence a uma empresa principal (Multi-empresa).
     */
    public function mainCompany()
    {
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    public function profile()
    {
        return $this->belongsTo(SensoryProfile::class, 'sensory_profile_id');
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}