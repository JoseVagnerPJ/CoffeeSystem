<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensoryProfileComponent extends Model
{
    protected $fillable = ['sensory_profile_id', 'sample_id', 'percentage'];

    public function profile()
    {
        return $this->belongsTo(SensoryProfile::class, 'sensory_profile_id');
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}