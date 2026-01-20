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
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->foreignId('cupping_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('sample_id')->nullable()->constrained()->onDelete('set null');
            
            $table->integer('position'); 
            $table->boolean('is_avulso')->default(false);
            $table->string('nickname')->nullable(); 

            // Snapshot dos dados técnicos
            $table->string('variety')->nullable();
            $table->string('drying_process')->nullable();
            $table->string('fermentation')->nullable();
            $table->string('humidity')->nullable();
            $table->string('screen')->nullable();
            $table->string('altitude')->nullable();
            $table->string('crop')->nullable();

            // COLUNAS FALTANTES QUE CAUSARAM O ERRO 500
            $table->unsignedBigInteger('origin_id')->nullable();
            $table->string('origin_type')->nullable();
            
            $table->text('notes')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cupping_items');
    }
};