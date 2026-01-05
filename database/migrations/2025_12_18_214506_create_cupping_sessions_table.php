<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuppingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
    Schema::create('cupping_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('title'); 
        $table->date('session_date');
        $table->foreignId('created_by')->constrained('users'); 
        $table->enum('status', ['aberto', 'finalizado'])->default('aberto');
        $table->text('notes')->nullable(); // Observações pré-prova da mesa
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
        Schema::dropIfExists('cupping_sessions');
    }
}
