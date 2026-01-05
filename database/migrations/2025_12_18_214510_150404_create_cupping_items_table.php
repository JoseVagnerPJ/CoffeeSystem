<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cupping_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupping_session_id')->constrained()->onDelete('cascade');
            
            // Vínculo opcional: se nulo, a xícara é "avulsa"
            $table->foreignId('sample_id')->nullable()->constrained(); 
            $table->boolean('is_avulso')->default(false);
            $table->integer('position'); // #1, #2... na mesa

            // Dados Técnicos (Para xícaras avulsas ou cópia da amostra)
            $table->string('variety')->nullable();
            $table->string('roast_type')->nullable(); // Adicionado para controle de torra
            $table->string('drying_process')->nullable();
            $table->string('fermentation')->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->string('screen')->nullable();
            $table->integer('altitude')->nullable();
            $table->string('crop')->nullable();
            
            // Origem/Fornecedor Polimórfico
            $table->nullableMorphs('origin'); 

            $table->text('notes')->nullable(); // Observações pré-prova (Ex: defeitos visuais)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cupping_items');
    }
};