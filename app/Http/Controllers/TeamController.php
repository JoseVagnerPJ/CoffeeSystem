<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainCompany;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Necessário para gerenciar arquivos

class TeamController extends Controller
{
    /**
     * Exibe todos os usuários (ativos e inativos).
     */
    public function index()
    {
        $company = Auth::user()->mainCompany;
        $profiles = Profile::where('main_company_id', $company->id)->get();
        
        // Puxa todos os usuários da unidade sem filtrar pelo status
        $users = User::where('main_company_id', $company->id)
                     ->with('profile')
                     ->get();

        // CORREÇÃO: Caminho atualizado para a nova pasta 'registries'
        return view('registries.team.index', compact('company', 'profiles', 'users'));
    }

    /**
     * Atualiza os dados da empresa e processa o upload da Logo.
     */
    public function updateCompany(Request $request)
    {
        $company = Auth::user()->mainCompany;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validação de imagem
        ]);

        $data = [
            'name' => $request->name,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj) // Salva apenas números
        ];

        // Se houver nova logo, apaga a antiga e salva a nova
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return redirect()->back()->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    /**
     * Cadastra um novo perfil de acesso.
     */
    public function storeProfile(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Profile::create([
            'main_company_id' => Auth::user()->main_company_id,
            'name' => $request->name,
            'permissions' => $request->permissions // Array convertido em JSON pelo Model
        ]);

        return redirect()->back()->with('success', 'Perfil criado!');
    }

    /**
     * Exclui um perfil de acesso.
     */
    public function destroyProfile($id)
    {
        $profile = Profile::where('main_company_id', Auth::user()->main_company_id)->findOrFail($id);
        $profile->delete();
        return redirect()->back()->with('success', 'Perfil removido!');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // A validação unique agora ignora quem não é da mesma empresa
            'username' => 'required|string|unique:users,username', 
            'password' => 'required|min:3',
            'profile_id' => 'required|exists:profiles,id',
            'photo' => 'nullable|image|max:2048'
        ]);

        $userData = [
            'main_company_id' => Auth::user()->main_company_id,
            'profile_id' => $request->profile_id,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_active' => true // GARANTA que isso seja true para ele aparecer na lista
        ];

        if ($request->hasFile('photo')) {
            $userData['photo'] = $request->file('photo')->store('users-photos', 'public');
        }

        User::create($userData);

        // CORREÇÃO: Rota de redirecionamento atualizada para o novo padrão registries.team
        return redirect()->route('registries.team.index')->with('success', 'Colaborador cadastrado e ativo!');
    }

    /**
     * Atualiza dados do colaborador e gerencia a substituição da foto.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::where('main_company_id', auth()->user()->main_company_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'profile_id' => 'required|exists:profiles,id', // Adicionado validação do perfil
            'password' => 'nullable|min:3',
            'photo' => 'nullable|image|max:2048'
        ]);

        // Atualizamos o nome e o perfil
        $data = [
            'name' => $request->name,
            'profile_id' => $request->profile_id
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('users-photos', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Dados atualizados com sucesso!');
    }

    /**
     * Inativa o colaborador e LIBERA o nome de usuário para novo cadastro.
     */
    public function inactivateUser($id)
    {
        $user = User::where('main_company_id', Auth::user()->main_company_id)->findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Você não pode alterar seu próprio status.');
        }

        // Inverte o status atual (Toggle)
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'ativado' : 'inativado';
        return redirect()->back()->with('success', "Colaborador {$status} com sucesso!");
    }
}