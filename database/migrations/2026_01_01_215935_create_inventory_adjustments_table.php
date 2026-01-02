<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            // FK para a sessão de inventário
            $table->foreignId('inventory_session_id')->constrained()->onDelete('cascade');
            
            // FK para o lote (Cascade permite excluir o lote sem erro de integridade)
            $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
            
            // Dados da Calculadora
            $table->integer('closed_bags')->default(0); 
            $table->decimal('bag_weight', 8, 2)->default(60); 
            $table->decimal('remainder_weight', 8, 2)->default(0); 
            
            // Pesos apurados
            $table->decimal('system_weight', 12, 2);
            $table->decimal('physical_weight', 12, 2);
            $table->decimal('difference_kg', 12, 2);

            // NOVO: Endereçamento capturado no inventário
            $table->string('new_address_row', 5)->nullable(); // Ex: 'A', 'B'
            $table->string('new_address_pos', 5)->nullable(); // Ex: '1', '2'
            
            $table->text('reason')->nullable(); 
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
        Schema::dropIfExists('inventory_adjustments');
    }
}