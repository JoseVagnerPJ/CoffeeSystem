<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainCompany;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Exibe todos os usuários (ativos e inativos).
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->mainCompany;

        // Bloqueio de segurança: Se não tem nenhuma das permissões de time, não acessa a tela
        if (!$user->hasPermission('team_full') && !$user->hasPermission('team_user_only')) {
            abort(403, 'Acesso Negado. Você não tem permissão para gerenciar a equipe.');
        }

        $profiles = Profile::where('main_company_id', $company->id)->get();
        
        $users = User::where('main_company_id', $company->id)
                     ->with('profile')
                     ->get();

        return view('registries.team.index', compact('company', 'profiles', 'users'));
    }

    /**
     * Atualiza os dados da empresa e processa o upload da Logo.
     */
    public function updateCompany(Request $request)
    {
        $company = auth()->user()->mainCompany;

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ]);

        if ($request->filled('name')) {
            $company->name = $request->name;
        }

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo = $path;
        }

        $company->save();
        return back()->with('success', 'Logotipo atualizado com sucesso!');
    }

    /**
     * Cadastra um novo perfil de acesso com trava de hierarquia.
     */
    public function storeProfile(Request $request)
    {
        // VALIDAÇÃO DE PERMISSÃO: Apenas permissão total pode gerenciar perfis
        if (!auth()->user()->hasPermission('team_full')) {
            return redirect()->back()->with('error', 'Você não tem permissão para gerenciar perfis de acesso.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $requestedPermissions = $request->permissions ?? [];
        $allowedPermissions = [];

        // TRAVA DE HIERARQUIA: Só permite salvar permissões que o criador já possui
        foreach ($requestedPermissions as $key => $value) {
            if (auth()->user()->hasPermission($key)) {
                $allowedPermissions[$key] = $value;
            }
        }

        Profile::create([
            'name' => $request->name,
            'permissions' => $allowedPermissions,
            'main_company_id' => auth()->user()->main_company_id,
        ]);

        return redirect()->back()->with('success', 'Nível de acesso configurado com base nas suas permissões!');
    }

    /**
     * Exclui um perfil de acesso (Protege o Desenvolvedor).
     */
    public function destroyProfile($id)
    {
        $profile = Profile::where('main_company_id', Auth::user()->main_company_id)->findOrFail($id);

        // IMUNIDADE: Perfil Desenvolvedor ou All Access não podem ser apagados
        $isDevProfile = ($profile->name === 'Desenvolvedor' || (isset($profile->permissions['dev_access']) && $profile->permissions['dev_access']));
        
        if ($isDevProfile) {
            return redirect()->back()->with('error', 'O perfil mestre do sistema é vitalício e não pode ser removido.');
        }

        // Apenas Admin Total ou Dev podem excluir perfis
        if (!auth()->user()->hasPermission('all_access') && !auth()->user()->hasPermission('dev_access')) {
            return redirect()->back()->with('error', 'Apenas administradores totais podem remover perfis de acesso.');
        }

        $profile->delete();
        return redirect()->back()->with('success', 'Perfil removido com sucesso!');
    }

    /**
     * Cria um novo usuário com validação de hierarquia e bloqueio de Dev.
     */
    public function storeUser(Request $request)
    {
        // Permite acesso para 'team_full' ou 'team_user_only'
        if (!auth()->user()->hasPermission('team_full') && !auth()->user()->hasPermission('team_user_only')) {
            return redirect()->back()->with('error', 'Você não tem permissão para cadastrar colaboradores.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username', 
            'password' => 'required|min:3',
            'profile_id' => 'required|exists:profiles,id',
            'photo' => 'nullable|image|max:20480'
        ]);

        $targetProfile = Profile::findOrFail($request->profile_id);

        // --- TRAVA DE SEGURANÇA: BLOQUEIO DE CRIAÇÃO DE DESENVOLVEDOR ---
        $isTargetDev = ($targetProfile->name === 'Desenvolvedor' || (isset($targetProfile->permissions['dev_access']) && $targetProfile->permissions['dev_access']));

        if ($isTargetDev && !auth()->user()->hasPermission('dev_access')) {
            return redirect()->back()->with('error', 'Não é permitido criar usuários com nível "Desenvolvedor".');
        }

        // TRAVA DE HIERARQUIA: Não permite criar usuário com cargo superior ao seu
        if (!auth()->user()->hasPermission('all_access')) {
            foreach ($targetProfile->permissions as $perm => $isActive) {
                if ($isActive && !auth()->user()->hasPermission($perm)) {
                    return redirect()->back()->with('error', 'Você não pode atribuir um perfil que possui permissões superiores às suas.');
                }
            }
        }

        $userData = [
            'main_company_id' => Auth::user()->main_company_id,
            'profile_id' => $request->profile_id,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_active' => true
        ];

        if ($request->hasFile('photo')) {
            $userData['photo'] = $request->file('photo')->store('users-photos', 'public');
        }

        User::create($userData);

        return redirect()->route('registries.team.index')->with('success', 'Colaborador cadastrado e ativo!');
    }

    /**
     * Atualiza dados do colaborador com validação de hierarquia e bloqueios de perfil.
     */
    public function updateUser(Request $request, $id)
    {
        $authUser = auth()->user();
        $userToEdit = User::where('main_company_id', $authUser->main_company_id)->findOrFail($id);

        // --- TRAVA DE IMUTABILIDADE: PROTEÇÃO DE USUÁRIOS DESENVOLVEDORES ---
        // Verifica se o usuário que está SENDO EDITADO já é um Desenvolvedor
        $userIsAlreadyDev = ($userToEdit->profile && ($userToEdit->profile->name === 'Desenvolvedor' || (isset($userToEdit->profile->permissions['dev_access']) && $userToEdit->profile->permissions['dev_access'])));

        if ($userIsAlreadyDev && !$authUser->hasPermission('dev_access')) {
            return redirect()->back()->with('error', 'Segurança: A ficha de um Desenvolvedor só pode ser alterada por outro Desenvolvedor.');
        }

        // 1. BLOQUEIO DE PERMISSÃO: Apenas quem tem 'team_full' pode editar.
        if (!$authUser->hasPermission('team_full')) {
            return redirect()->back()->with('error', 'Sua conta possui permissão apenas para criação de usuários, não para edição.');
        }

        // 2. PROTEÇÃO DO ADMINISTRADOR: Apenas um Administrador Mestre pode editar outro Administrador Mestre
        if ($userToEdit->hasPermission('all_access') && !$authUser->hasPermission('all_access')) {
            return redirect()->back()->with('error', 'Apenas um Administrador Mestre pode alterar dados de outro Administrador.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'profile_id' => 'required|exists:profiles,id',
            'password' => 'nullable|min:3',
            'photo' => 'nullable|image|max:20480'
        ]);

        $targetProfile = Profile::findOrFail($request->profile_id);

        // --- TRAVA DE SEGURANÇA: BLOQUEIO DE PROMOÇÃO PARA DESENVOLVEDOR ---
        $isTargetDev = ($targetProfile->name === 'Desenvolvedor' || (isset($targetProfile->permissions['dev_access']) && $targetProfile->permissions['dev_access']));

        if ($isTargetDev && !$authUser->hasPermission('dev_access')) {
            return redirect()->back()->with('error', 'O nível de acesso "Desenvolvedor" é restrito e não pode ser atribuído.');
        }

        // TRAVA DE HIERARQUIA: Impede promover alguém para um nível que você não alcança
        if (!$authUser->hasPermission('all_access')) {
            foreach ($targetProfile->permissions as $perm => $isActive) {
                if ($isActive && !$authUser->hasPermission($perm)) {
                    return redirect()->back()->with('error', 'Você não tem autoridade para atribuir este nível de acesso.');
                }
            }
        }

        $data = [
            'name' => $request->name,
            'profile_id' => $request->profile_id
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($userToEdit->photo) {
                Storage::disk('public')->delete($userToEdit->photo);
            }
            $path = $request->file('photo')->store('users-photos', 'public');
            $data['photo'] = $path;
        }

        $userToEdit->update($data);

        return redirect()->back()->with('success', 'Dados atualizados com sucesso!');
    }

    /**
     * Inativa o colaborador (Protege o Desenvolvedor e Admins).
     */
    public function inactivateUser($id)
    {
        $authUser = auth()->user();
        $userToChange = User::where('main_company_id', $authUser->main_company_id)->findOrFail($id);
        
        // BLOQUEIO DE PERMISSÃO: Apenas 'team_full' altera status
        if (!$authUser->hasPermission('team_full')) {
            return redirect()->back()->with('error', 'Você não tem permissão para alterar o status de colaboradores.');
        }

        if ($userToChange->id === $authUser->id) {
            return redirect()->back()->with('error', 'Você não pode alterar seu próprio status.');
        }

        // IMUNIDADE: Desenvolvedor ou Admin Total não podem ser inativados por quem tem acesso inferior
        $isProtected = ($userToChange->hasPermission('dev_access') || ($userToChange->hasPermission('all_access') && !$authUser->hasPermission('all_access')));
        
        if ($isProtected) {
            return redirect()->back()->with('error', 'Este usuário possui acesso vitalício ou superior e não pode ser alterado por você.');
        }

        $userToChange->update([
            'is_active' => !$userToChange->is_active
        ]);

        $status = $userToChange->is_active ? 'ativado' : 'inativado';
        return redirect()->back()->with('success', "Colaborador {$status} com sucesso!");
    }
}