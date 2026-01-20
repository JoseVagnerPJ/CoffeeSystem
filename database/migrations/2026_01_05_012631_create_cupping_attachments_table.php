<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Usar "return new class" elimina o conflito de nomes
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupping_attachments', function (Blueprint $table) {
            $table->id();
            // Coluna de isolamento para o terminal/empresa
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            
            // Vincula o anexo ao item da mesa de prova
            $table->foreignId('cupping_item_id')->constrained()->onDelete('cascade');
            
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
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
        Schema::dropIfExists('cupping_attachments');
    }
};