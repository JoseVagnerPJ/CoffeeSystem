<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuppingSessionsTable extends Migration
{
    public function up() {
        // Tabela Principal
        Schema::create('cupping_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->string('title'); 
            $table->date('session_date');
            $table->foreignId('created_by')->constrained('users'); 
            $table->enum('status', ['aberto', 'finalizado'])->default('aberto');
            $table->text('notes')->nullable(); 
            $table->timestamp('started_at')->nullable(); 
            $table->timestamp('finished_at')->nullable(); 
            $table->integer('items_count')->default(0);
            $table->timestamps();
        });

        // Tabela para controlar quem entrou na mesa e quem já terminou
        Schema::create('cupping_session_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupping_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('has_finished')->default(false); // Marca se esse usuário específico já enviou as notas
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('cupping_session_user');
        Schema::dropIfExists('cupping_sessions');
    }
}