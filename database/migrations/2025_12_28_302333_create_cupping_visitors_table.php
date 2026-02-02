<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cupping_visitors', function (Blueprint $table) {
            $table->id();
            
            // Vínculo com a mesa específica que o visitante está acessando
            $table->foreignId('cupping_session_id')->constrained()->onDelete('cascade');

            // Dados de identificação para o "usuário temporário"
            $table->string('name');
            $table->string('cpf');

            // Timestamps para saber quando o visitante entrou na mesa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupping_visitors');
    }
};