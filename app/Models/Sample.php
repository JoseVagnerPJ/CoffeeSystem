<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        'name', 'receipt_date', 'supplier_batch', 'variety', 
        'drying_process', 'fermentation', 'humidity', 
        'screen', 'altitude', 'crop', 'observations'
    ];

    // O "pulo do gato": permite que a amostra aponte para qualquer uma das 3 tabelas
    public function origin()
    {
        return $this->morphTo();
    }
}