<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenCoffeeLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('green_coffee_lots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quem cadastrou
        $table->string('reference');       // Nome fácil (Ex: "Lote Especial Jan/24")
        $table->string('origin');          // Produtor / Fornecedor
        $table->string('process');         // Natural, Lavado, etc.
        
        // Endereçamento simples
        $table->string('address_row');     // Letra (A, B, C)
        $table->integer('address_pos');    // Número (1, 2, 3)
        
        // Quantidades e Pesos
        $table->integer('quantity_bags');
        $table->decimal('unit_weight', 8, 2);  
        $table->decimal('total_weight', 10, 2); 
        
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
        Schema::dropIfExists('green_coffee_lots');
    }
}
