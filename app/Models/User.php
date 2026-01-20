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
        if (!$this->profile || !$this->is_active) {
            return false;
        }

        $permissions = $this->profile->permissions;

        // Chave Mestra: Se tiver all_access ou for Dev, retorna true para tudo
        if ((isset($permissions['all_access']) && $permissions['all_access']) || 
            (isset($permissions['dev_access']) && $permissions['dev_access'])) {
            return true;
        }

        return isset($permissions[$permission]) && $permissions[$permission];
    }
}