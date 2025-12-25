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
    // Filtra para trazer apenas os registros que NÃO foram "excluídos"
    $producers = \App\Models\Producer::where('is_active', true)->get();
    $farms = \App\Models\Farm::where('is_active', true)->get();
    $companies = \App\Models\Company::where('is_active', true)->get();

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
        // Vincular produtor a fazenda
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

        // Vincular produtor a empresa
        if (session('captured_producer_id') && $targetType == 'company') {
        DB::table('origin_links')->insert([
            'producer_id' => session('captured_producer_id'),
            'company_id' => $targetId,
            'created_at' => now(), 'updated_at' => now()
        ]);
        $this->clearCaptureInternal();
        return redirect()->back()->with('success', 'Produtor vinculado à Empresa/Cooperativa!');
        }

        // Vincular fazenda a empresa
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

    public function inactivate($type, $id)
    {
    // 1. Busca o item
    if ($type === 'producer') {
        $item = \App\Models\Producer::findOrFail($id);
    } elseif ($type === 'farm') {
        $item = \App\Models\Farm::findOrFail($id);
    } elseif ($type === 'company') {
        $item = \App\Models\Company::findOrFail($id);
    } else {
        return redirect()->back()->with('error', 'Tipo inválido.');
    }

    // 2. Força a alteração manualmente (isso ignora o $fillable)
    $item->is_active = 0; // Usando 0 explicitamente para garantir o boolean false
    $item->save();

    // 3. TESTE DE DEPURAÇÃO: 
    // Se você chegar na página preta e 'is_active' estiver 0, o código funcionou.
    // dd($item->toArray()); 

    return redirect()->back()->with('success', 'Cadastro removido com sucesso.');
    }
}