<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sample;
use App\Models\Company;
use App\Models\Producer;
use App\Models\Farm;

class SampleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sample::with('origin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_batch', 'LIKE', "%{$search}%")
                  ->orWhere('variety', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('date_start')) $query->whereDate('receipt_date', '>=', $request->date_start);
        if ($request->filled('date_end')) $query->whereDate('receipt_date', '<=', $request->date_end);
        if ($request->filled('filter_variety')) $query->where('variety', 'LIKE', "%{$request->filter_variety}%");
        if ($request->filled('filter_fermentation')) $query->where('fermentation', 'LIKE', "%{$request->filter_fermentation}%");
        if ($request->filled('filter_sensory')) $query->where('observations', 'LIKE', "%{$request->filter_sensory}%");

        if ($request->filled('filter_origin_id') && $request->filled('filter_origin_type')) {
            $query->where('origin_id', $request->filter_origin_id)->where('origin_type', $request->filter_origin_type);
        }

        $samples = $query->latest()->get();
        $companies = Company::where('is_active', 1)->orderBy('name')->get();
        $producers = Producer::where('is_active', 1)->orderBy('name')->get();
        $farms = Farm::where('is_active', 1)->orderBy('name')->get();

        return view('samples.index', compact('samples', 'companies', 'producers', 'farms'));
    }

    public function info($id) {
        $sample = Sample::with(['origin', 'sensoryProfileComponents.profile'])->findOrFail($id);
        return view('samples.partials.info', compact('sample'));
    }

    public function store(Request $request) {
        $request->validate(['origin_id' => 'required', 'origin_type' => 'required', 'name' => 'required']);
        Sample::create($request->all());
        return redirect()->route('samples.index')->with('success', 'Amostra cadastrada!');
    }

    public function searchOrigin(Request $request) {
        $q = $request->get('q');
        $results = [];
        $companies = Company::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($companies as $c) { $results[] = ['id' => $c->id, 'type' => 'App\Models\Company', 'name' => $c->name, 'label' => 'Empresa']; }
        $producers = Producer::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($producers as $p) { $results[] = ['id' => $p->id, 'type' => 'App\Models\Producer', 'name' => $p->name, 'label' => 'Produtor']; }
        $farms = Farm::where('is_active', 1)->where('name', 'LIKE', "%{$q}%")->limit(5)->get();
        foreach ($farms as $f) { $results[] = ['id' => $f->id, 'type' => 'App\Models\Farm', 'name' => $f->name, 'label' => 'Fazenda']; }
        return response()->json($results);
    }

    public function destroy($id) {
        Sample::findOrFail($id)->delete();
        return redirect()->route('samples.index')->with('success', 'Amostra excluída!');
    }
}