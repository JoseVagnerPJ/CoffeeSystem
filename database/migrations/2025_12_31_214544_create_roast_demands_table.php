<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastDemandsTable extends Migration
{
    public function up(): void 
    {
        Schema::create('roast_demands', function (Blueprint $table) {
            $table->id();
            
            // CORREÇÃO 1: Removido o ->constrained() pois a tabela 'machines' é do futuro (dia 25/12)
            // CORREÇÃO 2: Nome da tabela ajustado para 'machines'
            $table->unsignedBigInteger('machine_id')->nullable();
            
            $table->foreignId('manager_id')->constrained('users');
            $table->foreignId('assigned_mestre_id')->constrained('users');
            $table->foreignId('green_coffee_lot_id')->constrained();
            
            $table->integer('number_of_batches');
            $table->decimal('weight_per_batch', 7, 2);
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->text('instructions')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roast_demands');
    }
}