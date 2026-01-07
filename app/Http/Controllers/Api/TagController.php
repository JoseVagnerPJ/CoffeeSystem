<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Retorna todas as notas sensoriais cadastradas para o autocomplete do App.
     */
    public function index()
    {
        // Retorna as tags ordenadas por nome
        return response()->json(Tag::orderBy('name', 'asc')->get(['id', 'name']));
    }
}