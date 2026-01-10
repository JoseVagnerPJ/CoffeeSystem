<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensoryProfile;
use App\Models\SensoryProfileComponent;
use App\Models\Sample;
use Illuminate\Support\Facades\DB;

class SensoryProfileController extends Controller
{
public function index(Request $request)
    {
        $query = SensoryProfile::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }
        $profiles = $query->latest()->get();

        // ADICIONADO: Retorno JSON para o autocomplete do Lote Verde
        if ($request->wantsJson() || $request->get('format') == 'json') {
            return response()->json($profiles);
        }

        return view('registries.sensory-profiles.index', compact('profiles'));
    }
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'sample_id' => 'required|array',
        'percentage' => 'required|array'
    ]);

    $total = array_sum($request->percentage);
    if (abs($total - 100) > 0.01) {
        return redirect()->back()->withInput()->withErrors(['blend_error' => "A soma deve ser 100%."]);
    }

    DB::transaction(function () use ($request) {
        // Captura os dados e trata o campo is_blend (Checkbox/Switch)
        $data = $request->only([
            'name', 'roast_intensity', 'color_hex', 'notes', 
            'sweetness', 'acidity', 'body', 'bitterness'
        ]);
        $data['is_blend'] = $request->has('is_blend') ? 1 : 0;

        $profile = SensoryProfile::create($data);

        foreach ($request->sample_id as $index => $id) {
            if ($id) {
                SensoryProfileComponent::create([
                    'sensory_profile_id' => $profile->id,
                    'sample_id' => $id,
                    'percentage' => $request->percentage[$index]
                ]);
            }
        }
    });

    return redirect()->route('registries.sensory-profiles.index')->with('success', 'Perfil cadastrado!');
}
    public function searchSamples(Request $request)
    {
        $q = $request->get('q');
        
        // CORREÇÃO: Removido 'is_active' pois a coluna não existe no seu banco
        $samples = Sample::where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('variety', 'LIKE', "%{$q}%")
                      ->orWhere('supplier_batch', 'LIKE', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'variety', 'supplier_batch']);

        return response()->json($samples);
    }

    public function show($id) {
        $profile = SensoryProfile::with('components.sample.origin')->findOrFail($id);
        // Caminho da view de info atualizado (dentro da subpasta partials)
        return view('registries.sensory-profiles.partials.info', compact('profile'));
    }

    public function destroy($id) {
        SensoryProfile::findOrFail($id)->delete();
        // Rota de redirecionamento atualizada
        return redirect()->route('registries.sensory-profiles.index')->with('success', 'Perfil removido!');
    }
}