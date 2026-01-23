<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\BelongsToCompany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id', 'profile_id', 'name', 'username', 'password', 'is_active', 'photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relacionamento com a Empresa Dona do sistema
    public function mainCompany()
    {
        // Aqui o nome é mainCompany, deve ser o mesmo no Controller
        return $this->belongsTo(MainCompany::class, 'main_company_id');
    }

    // Relacionamento com o Perfil de Permissões
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Relacionamentos de Atividade do Usuário no Sistema
     */
    public function cuppingSessions() { return $this->hasMany(CuppingSession::class, 'created_by'); }
    public function greenCoffeeLots() { return $this->hasMany(GreenCoffeeLot::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }
    public function inventorySessions() { return $this->hasMany(InventorySession::class); }
    public function roastDemands() { return $this->hasMany(RoastDemand::class, 'user_id'); }
    public function roastDemandsAsMestre() { return $this->hasMany(RoastDemand::class, 'mestre_user_id'); }
    public function roastingBatches() { return $this->hasMany(RoastingBatch::class, 'mestre_user_id'); }
    public function cups() { return $this->hasMany(Cup::class); }
    public function cleaningLogs() { return $this->hasMany(MachineCleaningLog::class); }

public function hasPermission($permission)
{
    // 1. Segurança básica: se não houver perfil ou o usuário estiver inativo, nega tudo.
    if (!$this->profile || !$this->is_active) {
        return false;
    }

    $permissions = $this->profile->permissions ?? [];

    // 2. REGRA PARA O PORTAL MASTER:
    // Se a pergunta for especificamente sobre 'dev_access', 
    // respondemos TRUE apenas se o usuário REALMENTE tiver a chave dev_access.
    if ($permission === 'dev_access') {
        return isset($permissions['dev_access']) && ($permissions['dev_access'] == true || $permissions['dev_access'] == "1");
    }

    // 3. REGRA PARA ADMINISTRADORES (Mestra):
    // Se o usuário for um Desenvolvedor OU um Administrador (all_access),
    // ele tem permissão para qualquer OUTRA função do sistema automaticamente.
    $isDev = isset($permissions['dev_access']) && ($permissions['dev_access'] == true || $permissions['dev_access'] == "1");
    $isMaster = isset($permissions['all_access']) && ($permissions['all_access'] == true || $permissions['all_access'] == "1");

    if ($isDev || $isMaster) {
        return true;
    }

    // 4. CHECAGEM INDIVIDUAL:
    // Se não for admin/dev, verifica se a chave específica existe e é verdadeira.
    return isset($permissions[$permission]) && ($permissions[$permission] == true || $permissions[$permission] == "1");
}
}