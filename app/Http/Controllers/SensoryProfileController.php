<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensoryProfile;

class SensoryProfileController extends Controller
{
    public function index(Request $request)
    {
        $profiles = SensoryProfile::latest()->get();
        return view('sensory_profiles.index', compact('profiles'));
    }

    public function show($id) {
        $profile = SensoryProfile::with('components.sample.origin')->findOrFail($id);
        return view('sensory_profiles.partials.info', compact('profile'));
    }
}