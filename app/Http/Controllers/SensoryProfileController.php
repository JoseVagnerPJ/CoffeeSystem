<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensoryProfile;
use App\Models\SensoryProfileComponent;
use App\Models\Sample;
use Illuminate\Support\Facades\DB;

class SensoryProfileController extends Controller
{
    public function index()
    {
        $profiles = SensoryProfile::latest()->get();
        return view('sensory_profiles.index', compact('profiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sample_id' => 'required|array',
            'percentage' => 'required|array',
        ]);

        $totalPercentage = array_sum($request->percentage);
        
        if (abs($totalPercentage - 100) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['blend_error' => "A soma das porcentagens é {$totalPercentage}%. Ela deve ser exatamente 100%."]);
        }

        DB::transaction(function () use ($request) {
            $profile = SensoryProfile::create([
                'name' => $request->name,
                'is_blend' => $request->has('is_blend'),
                'roast_intensity' => $request->roast_intensity,
                'color_hex' => $request->color_hex,
                'notes' => $request->notes,
                'sweetness' => $request->sweetness,
                'acidity' => $request->acidity,
                'body' => $request->body,
                'bitterness' => $request->bitterness,
            ]);

            foreach ($request->sample_id as $index => $sampleId) {
                SensoryProfileComponent::create([
                    'sensory_profile_id' => $profile->id,
                    'sample_id' => $sampleId,
                    'percentage' => $request->percentage[$index],
                ]);
            }
        });

        return redirect()->route('sensory-profiles.index')->with('success', 'Perfil Sensorial criado com sucesso!');
    }

    public function show($id)
    {
        // Busca o perfil com componentes e a origem de cada amostra
        $profile = SensoryProfile::with('components.sample.origin')->findOrFail($id);
        
        // Retorna a partial que será injetada no modal via JS
        return view('sensory_profiles.partials.info', compact('profile'));
    }

    public function searchSamples(Request $request)
    {
        $q = $request->get('q');
        $samples = Sample::where('name', 'LIKE', "%{$q}%")
            ->orWhere('variety', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get();

        return response()->json($samples);
    }

    public function destroy($id)
    {
        $profile = SensoryProfile::findOrFail($id);
        $profile->delete();
        return redirect()->route('sensory-profiles.index')->with('success', 'Perfil removido.');
    }
}