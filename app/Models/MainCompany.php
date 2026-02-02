<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'cnpj', 
        'logo',
        'map_view', 
        'is_active', 
        'mod_lab', 
        'mod_stock', 
        'mod_roast', 
        'mod_prod', 
        'mod_logistics', 
        'last_payment_date'
    ];

    /**
     * Casts para garantir tipos de dados corretos.
     */
    protected $casts = [
        'is_active'     => 'boolean',
        'mod_lab'       => 'boolean',
        'mod_stock'     => 'boolean',
        'mod_roast'     => 'boolean',
        'mod_prod'      => 'boolean',
        'mod_logistics' => 'boolean',
        'last_payment_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos Principais
    |--------------------------------------------------------------------------
    */

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'main_company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CompanyPayment::class, 'main_company_id')->orderBy('payment_date', 'desc');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'main_company_id');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'main_company_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos de Módulos (Estoque, Laboratório, Torra, etc)
    |--------------------------------------------------------------------------
    */

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class, 'main_company_id');
    }

    public function producers(): HasMany
    {
        return $this->hasMany(Producer::class, 'main_company_id');
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class, 'main_company_id');
    }

    public function sensoryProfiles(): HasMany
    {
        return $this->hasMany(SensoryProfile::class, 'main_company_id');
    }

    public function sensoryProfileComponents(): HasMany
    {
        return $this->hasMany(SensoryProfileComponent::class, 'main_company_id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'main_company_id');
    }

    public function greenCoffeeLots(): HasMany
    {
        return $this->hasMany(GreenCoffeeLot::class, 'main_company_id');
    }

    public function lotAttachments(): HasMany
    {
        return $this->hasMany(LotAttachment::class, 'main_company_id');
    }

    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class, 'main_company_id');
    }

    public function machineMaintenances(): HasMany
    {
        return $this->hasMany(MachineMaintenance::class, 'main_company_id');
    }

    public function cleaningLogs(): HasMany
    {
        return $this->hasMany(MachineCleaningLog::class, 'main_company_id');
    }

    public function roastDemands(): HasMany
    {
        return $this->hasMany(RoastDemand::class, 'main_company_id');
    }

    public function roastingBatches(): HasMany
    {
        return $this->hasMany(RoastingBatch::class, 'main_company_id');
    }

    public function roastLogs(): HasMany
    {
        return $this->hasMany(RoastLog::class, 'main_company_id');
    }

    public function cuppingSessions(): HasMany
    {
        return $this->hasMany(CuppingSession::class, 'main_company_id');
    }

    public function cuppingItems(): HasMany
    {
        return $this->hasMany(CuppingItem::class, 'main_company_id');
    }

    public function cuppingAttachments(): HasMany
    {
        return $this->hasMany(CuppingAttachment::class, 'main_company_id');
    }

    public function cups(): HasMany
    {
        return $this->hasMany(Cup::class, 'main_company_id');
    }

    public function inventorySessions(): HasMany
    {
        return $this->hasMany(InventorySession::class, 'main_company_id');
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'main_company_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'main_company_id');
    }

    public function lotLocations(): HasMany
    {
        return $this->hasMany(GreenCoffeeLotLocation::class, 'main_company_id');
    }

    public function blendCompositions(): HasMany
    {
        return $this->hasMany(BlendComposition::class, 'main_company_id');
    }
}