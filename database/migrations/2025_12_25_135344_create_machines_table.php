<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
    Schema::create('machines', function (Blueprint $table) {
        $table->id();
        // Adicionado para o controle multi-unidade do Ctrl+Café
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade'); 
        
        $table->string('name');
        $table->string('brand');
        $table->string('model');
        $table->integer('year')->nullable();
        $table->string('serial_number')->nullable();
        $table->decimal('max_capacity', 8, 2);
        $table->decimal('min_capacity', 8, 2);
        $table->integer('cleaning_threshold'); // Limite de torras antes da limpeza
        $table->integer('current_roasts')->default(0); // Contador atual
        $table->boolean('is_active')->default(true); // Para a exclusão lógica
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
        Schema::dropIfExists('machines');
    }
}
