<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('logistic_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('main_company_id')->constrained();
    $table->unsignedBigInteger('omie_order_id')->unique(); // ID Interno
    $table->string('order_number', 20);                  // O pedido Nº...
    $table->string('nf_number', 20)->nullable();         // Número da Nota
    
    $table->string('client_name');                       // Nome Fantasia
    $table->string('client_full_name');                  // Razão Social
    $table->string('client_cnpj', 25);
    $table->string('client_cep', 15)->nullable();
    $table->string('client_city', 100)->nullable();
    $table->string('client_uf', 2)->nullable();
    
    $table->decimal('merchandise_total', 15, 2)->default(0);
    $table->decimal('order_total', 15, 2)->default(0);
    $table->decimal('total_weight', 10, 3)->default(0);
    
    $table->string('carrier_name')->nullable();          // Nome da Transportadora
    $table->string('shipping_type', 50)->nullable();     // CIF/FOB
    $table->decimal('shipping_value', 15, 2)->default(0);
    
    $table->text('omie_obs')->nullable();                // Observações de venda
    $table->text('additional_info')->nullable();         // Informações adicionais NF
    $table->string('detected_rule')->nullable();
    $table->string('status')->default('importado');
    $table->string('omie_stage')->nullable();
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistic_orders');
    }
}
