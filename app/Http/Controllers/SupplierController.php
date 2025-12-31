<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producer;
use App\Models\Farm;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $filterType = $request->query('filter_type');
        $activeTab = $request->query('tab', 'producers');

        // Lógica compatível com PHP 7.x usando switch
        if ($filterType) {
            switch ($filterType) {
                case 'producer': $activeTab = 'producers'; break;
                case 'farm':     $activeTab = 'farms';     break;
                case 'company':  $activeTab = 'companies'; break;
            }
        }

        // --- PRODUTORES ---
        $producersQuery = Producer::where('is_active', true)->with(['farms', 'companies', 'samples']);
        if ($search) {
            $producersQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")->orWhere('cpf', 'LIKE', "%{$search}%");
            });
        }
        $producers = $producersQuery->latest()->get();

        // --- FAZENDAS ---
        $farmsQuery = Farm::where('is_active', true)->with(['producers', 'companies', 'samples']);
        if ($search) $farmsQuery->where('name', 'LIKE', "%{$search}%");
        if ($request->filled('filter_state')) $farmsQuery->where('state', 'LIKE', "%{$request->filter_state}%");
        if ($request->filled('filter_intl')) $farmsQuery->where('is_international', true);
        if ($request->filled('filter_altitude')) $farmsQuery->where('altitude', '>=', $request->filter_altitude);
        if ($request->filled('filter_variety')) $farmsQuery->where('varieties_tags', 'LIKE', "%{$request->filter_variety}%");
        $farms = $farmsQuery->latest()->get();

        // --- EMPRESAS ---
        $companiesQuery = Company::where('is_active', true)->with(['farms', 'producers', 'samples']);
        if ($search) $companiesQuery->where('name', 'LIKE', "%{$search}%");
        if ($request->filled('filter_state')) $companiesQuery->where('state', 'LIKE', "%{$request->filter_state}%");
        if ($request->filled('filter_action')) $companiesQuery->where('action_type', $request->filter_action);
        $companies = $companiesQuery->latest()->get();

        return view('suppliers.index', compact('producers', 'farms', 'companies', 'activeTab'));
    }

    public function inactivate(Request $request, $type, $id)
    {
        switch ($type) {
            case 'producer': $model = Producer::class; break;
            case 'farm':     $model = Farm::class;     break;
            case 'company':  $model = Company::class;  break;
            default: return redirect()->back()->with('error', 'Tipo inválido.');
        }

        $item = $model::findOrFail($id);
        $item->is_active = 0;
        $item->save();
        return redirect()->route('suppliers.index', ['tab' => $request->tab])->with('success', 'Removido!');
    }

    public function storeProducer(Request $request) { Producer::create($request->all()); return redirect()->route('suppliers.index', ['tab' => 'producers']); }
    public function storeFarm(Request $request) { $data = $request->all(); $data['is_international'] = $request->has('is_international'); Farm::create($data); return redirect()->route('suppliers.index', ['tab' => 'farms']); }
    public function storeCompany(Request $request) { $data = $request->all(); $data['is_international'] = $request->has('is_international'); Company::create($data); return redirect()->route('suppliers.index', ['tab' => 'companies']); }
    
    public function capture($type, $id) {
        $this->clearCaptureInternal();
        $item = ($type == 'producer') ? Producer::findOrFail($id) : Farm::findOrFail($id);
        session(["captured_{$type}_id" => $id, 'captured_name' => $item->name]);
        return redirect()->back();
    }

    public function clearCapture() { $this->clearCaptureInternal(); return redirect()->back(); }

    private function clearCaptureInternal() {
        session()->forget(['captured_producer_id', 'captured_farm_id', 'captured_name']);
    }

    public function link($targetType, $targetId) {
        if (session('captured_producer_id') && $targetType == 'farm') {
            DB::table('origin_links')->insert(['producer_id' => session('captured_producer_id'), 'farm_id' => $targetId, 'created_at' => now(), 'updated_at' => now()]);
            $this->clearCaptureInternal();
            return redirect()->route('suppliers.index', ['tab' => 'farms'])->with('success', 'Vinculado!');
        }
        if ((session('captured_producer_id') || session('captured_farm_id')) && $targetType == 'company') {
            DB::table('origin_links')->insert(['producer_id' => session('captured_producer_id'), 'farm_id' => session('captured_farm_id'), 'company_id' => $targetId, 'created_at' => now(), 'updated_at' => now()]);
            $this->clearCaptureInternal();
            return redirect()->route('suppliers.index', ['tab' => 'companies'])->with('success', 'Vinculado!');
        }
        return redirect()->back()->with('error', 'Ação inválida.');
    }
}