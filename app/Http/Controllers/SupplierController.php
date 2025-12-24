<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producer;
use App\Models\Farm;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
   public function index()
{
    // O .with() é obrigatório para carregar os vínculos
    $producers = Producer::with('farms')->get();
    $farms = Farm::with(['producers', 'companies'])->get();
    $companies = Company::with('farms')->get();

    return view('suppliers.index', compact('producers', 'farms', 'companies'));
}

    // --- LÓGICA DE CAPTURA (Igual ao Estoque) ---
    public function capture($type, $id)
    {
        // Limpa capturas anteriores para não confundir
        $this->clearCaptureInternal();

        if ($type == 'producer') {
            $item = Producer::findOrFail($id);
            session(['captured_producer_id' => $id, 'captured_name' => $item->name]);
        } elseif ($type == 'farm') {
            $item = Farm::findOrFail($id);
            session(['captured_farm_id' => $id, 'captured_name' => $item->name]);
        }

        return redirect()->back()->with('success', "{$item->name} capturado!");
    }

    public function clearCapture()
    {
        $this->clearCaptureInternal();
        return redirect()->back();
    }

    private function clearCaptureInternal()
    {
        session()->forget(['captured_producer_id', 'captured_farm_id', 'captured_name']);
    }

    // --- LÓGICA DE VÍNCULO ---
    public function link($targetType, $targetId)
    {
        // Se temos um Produtor na mão e clicamos em Vincular numa Fazenda
        if (session('captured_producer_id') && $targetType == 'farm') {
            DB::table('origin_links')->insert([
                'producer_id' => session('captured_producer_id'),
                'farm_id' => $targetId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->clearCaptureInternal();
            return redirect()->back()->with('success', 'Produtor vinculado à Fazenda com sucesso!');
        }

        // Se temos uma Fazenda na mão e clicamos em Vincular numa Empresa
        if (session('captured_farm_id') && $targetType == 'company') {
            DB::table('origin_links')->insert([
                'farm_id' => session('captured_farm_id'),
                'company_id' => $targetId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->clearCaptureInternal();
            return redirect()->back()->with('success', 'Fazenda vinculada à Empresa com sucesso!');
        }

        return redirect()->back()->with('error', 'Combinação de vínculo inválida.');
    }
    // Método para salvar Produtor
    public function storeProducer(Request $request) {
        Producer::create($request->all());
        return redirect()->back()->with('success', 'Produtor cadastrado!');
    }

    // Método para salvar Fazenda
    public function storeFarm(Request $request) {
        $data = $request->all();
        $data['is_international'] = $request->has('is_international');
        Farm::create($data);
        return redirect()->back()->with('success', 'Fazenda cadastrada!');
    }

    // Método para salvar Empresa
    public function storeCompany(Request $request) {
        $data = $request->all();
        $data['is_international'] = $request->has('is_international');
        Company::create($data);
        return redirect()->back()->with('success', 'Empresa cadastrada!');
    }
}