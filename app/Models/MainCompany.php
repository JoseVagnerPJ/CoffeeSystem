<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCompany extends Model
{
    use HasFactory;

    protected $fillable = [
    'name', 'cnpj', 'logo','map_view', 'is_active', 
    'mod_lab', 'mod_stock', 'mod_roast', 'mod_prod', 
    'last_payment_date'
];

    /**
     * Relacionamento: Uma empresa possui muitos usuários.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'main_company_id');
    }

    public function payments()
{
    return $this->hasMany(CompanyPayment::class, 'main_company_id')->orderBy('payment_date', 'desc');
}

    /**
     * Relacionamento: Uma empresa possui muitos perfis de permissão.
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class, 'main_company_id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'main_company_id');
    }

    public function farms()
    {
        return $this->hasMany(Farm::class, 'main_company_id');
    }

    public function producers()
    {
        return $this->hasMany(Producer::class, 'main_company_id');
    }

    public function samples()
    {
        return $this->hasMany(Sample::class, 'main_company_id');
    }

    public function sensoryProfiles()
    {
        return $this->hasMany(SensoryProfile::class, 'main_company_id');
    }

    public function sensoryProfileComponents()
    {
        return $this->hasMany(SensoryProfileComponent::class, 'main_company_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'main_company_id');
    }

    public function greenCoffeeLots()
    {
        return $this->hasMany(GreenCoffeeLot::class, 'main_company_id');
    }

    public function lotAttachments()
    {
        return $this->hasMany(LotAttachment::class, 'main_company_id');
    }

    public function machines()
    {
        return $this->hasMany(Machine::class, 'main_company_id');
    }

    public function machineMaintenances()
    {
        return $this->hasMany(MachineMaintenance::class, 'main_company_id');
    }

    public function cleaningLogs()
    {
        return $this->hasMany(MachineCleaningLog::class, 'main_company_id');
    }

    public function roastDemands()
    {
        return $this->hasMany(RoastDemand::class, 'main_company_id');
    }

    public function roastingBatches()
    {
        return $this->hasMany(RoastingBatch::class, 'main_company_id');
    }

    public function roastLogs()
    {
        return $this->hasMany(RoastLog::class, 'main_company_id');
    }

    public function cuppingSessions()
    {
        return $this->hasMany(CuppingSession::class, 'main_company_id');
    }

    public function cuppingItems()
    {
        return $this->hasMany(CuppingItem::class, 'main_company_id');
    }

    public function cuppingAttachments()
    {
        return $this->hasMany(CuppingAttachment::class, 'main_company_id');
    }

    public function cups()
    {
        return $this->hasMany(Cup::class, 'main_company_id');
    }

    public function inventorySessions()
    {
        return $this->hasMany(InventorySession::class, 'main_company_id');
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'main_company_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'main_company_id');
    }

    public function lotLocations()
    {
        return $this->hasMany(GreenCoffeeLotLocation::class, 'main_company_id');
    }

    public function blendCompositions()
    {
        return $this->hasMany(BlendComposition::class, 'main_company_id');
    }
}