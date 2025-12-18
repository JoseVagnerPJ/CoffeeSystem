<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    // O Laravel assume que a tabela é o plural do nome (companies), então já está certo.
    protected $fillable = ['name', 'cnpj', 'logo'];
}