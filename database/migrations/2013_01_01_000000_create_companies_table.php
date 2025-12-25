<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up(): void 
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('action_type')->nullable(); // Ex: Cooperativa, Exportadora
            $table->string('cnpj', 20)->unique()->nullable();
            $table->string('contact')->nullable();
            
            // Campos de Localização
            $table->boolean('is_international')->default(false);
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('country')->nullable();
            
            // Controle de Rastreabilidade (Inativação)
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
}