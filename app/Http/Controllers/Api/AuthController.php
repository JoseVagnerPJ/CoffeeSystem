<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MainCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
public function login(Request $request) {
    return response()->json(['message' => 'O endpoint está vivo!'], 200);
}
}