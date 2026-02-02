<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class LogisticOrder extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'main_company_id', 'omie_order_id', 'order_number', 'nf_number',
        'client_name', 'client_full_name', 'client_cnpj', 'client_cep','client_city',
        'merchandise_total', 'order_total', 'total_weight',
        'carrier_name', 'shipping_type', 'shipping_value',
        'omie_obs', 'additional_info', 'detected_rule', 'status', 'omie_stage'
    ];

    protected $casts = [
        'omie_order_id'     => 'integer',
        'merchandise_total' => 'float',
        'order_total'       => 'float',
        'total_weight'      => 'float',
        'shipping_value'    => 'float',
    ];

    /**
     * Relacionamento com os Itens do Pedido
     */
    public function items()
    {
        return $this->hasMany(LogisticOrderItem::class);
    }

    /**
     * Relacionamento com os Departamentos
     */
    public function departments()
    {
        return $this->hasMany(LogisticOrderDepartment::class);
    }
}