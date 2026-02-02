<?php

namespace App\Http\Controllers;

use App\Models\CuppingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuppingProfileController extends Controller
{
    /**
     * Lista os perfis de prova da empresa logada.
     */
    public function index()
    {
        $profiles = CuppingProfile::where('main_company_id', Auth::user()->main_company_id)
            ->latest()
            ->get();

        return view('registries.cupping-profiles.index', compact('profiles'));
    }

    /**
     * Salva um novo perfil de prova.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Lista de todos os campos booleanos do formulário
        $booleanFields = [
            'eval_fragrance', 'eval_flavor', 'eval_aftertaste', 'eval_acidity',
            'eval_body', 'eval_balance', 'eval_uniformity', 'eval_clean_cup',
            'eval_sweetness', 'eval_overall', 'eval_defects',
            'show_roast_level', 'show_fragrance_scales', 'show_intensity_scales', 'show_defect_details'
        ];

        $data = $request->only(['name', 'description']);
        $data['main_company_id'] = Auth::user()->main_company_id;

        // Lógica para Checkboxes: Se não estiver no request, definimos como false (0)
        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }

        CuppingProfile::create($data);

        return redirect()->route('registries.cupping-profiles.index')
            ->with('success', 'Perfil de prova criado com sucesso!');
    }

    /**
     * Atualiza um perfil existente.
     */
    public function update(Request $request, $id)
    {
        $profile = CuppingProfile::where('main_company_id', Auth::user()->main_company_id)
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $booleanFields = [
            'eval_fragrance', 'eval_flavor', 'eval_aftertaste', 'eval_acidity',
            'eval_body', 'eval_balance', 'eval_uniformity', 'eval_clean_cup',
            'eval_sweetness', 'eval_overall', 'eval_defects',
            'show_roast_level', 'show_fragrance_scales', 'show_intensity_scales', 'show_defect_details'
        ];

        $data = $request->only(['name', 'description']);

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }

        $profile->update($data);

        return redirect()->route('registries.cupping-profiles.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Remove um perfil de prova.
     */
    public function destroy($id)
    {
        $profile = CuppingProfile::where('main_company_id', Auth::user()->main_company_id)
            ->findOrFail($id);

        // Opcional: Impedir exclusão se o perfil estiver em uso por alguma mesa
        if ($profile->main_company_id !== Auth::user()->main_company_id) {
            return back()->with('error', 'Ação não autorizada.');
        }

        $profile->delete();

        return redirect()->route('registries.cupping-profiles.index')
            ->with('success', 'Perfil removido com sucesso!');
    }
}