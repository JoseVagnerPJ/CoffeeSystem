<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Usar "return new class" elimina o erro de classe duplicada
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupping_items', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos
            $table->foreignId('cupping_session_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->foreignId('sample_id')
                  ->constrained(); // Vincula o Lote/Amostra

            // Atributos da Mesa
            $table->integer('position'); // #1, #2, #3... na mesa de prova
            $table->string('blind_code')->nullable(); // Para o modo de prova cega
            
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
        Schema::dropIfExists('cupping_items');
    }
};