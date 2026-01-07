<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function cups(): BelongsToMany
    {
        return $this->belongsToMany(Cup::class, 'cup_tag');
    }
}