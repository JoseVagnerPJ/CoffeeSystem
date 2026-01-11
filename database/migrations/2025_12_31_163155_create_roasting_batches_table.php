<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastingBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
Schema::create('roasting_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('roast_demand_id')->constrained()->onDelete('cascade');
    $table->foreignId('mestre_user_id')->constrained('users'); // Quem executou este ciclo
    
    $table->integer('batch_number'); 
    $table->decimal('weight_in', 8, 2); 
    $table->decimal('weight_out', 8, 2)->nullable(); 
    
    // Para agrupar torras pesadas juntas no mesmo Brute
    $table->string('brute_group_id')->nullable(); 

    $table->timestamp('started_at')->nullable();
    $table->timestamp('finished_at')->nullable();
    
    $table->string('status')->default('pending'); // pending, roasting, completed, wasted
    $table->text('notes')->nullable();
    $table->boolean('is_special_tour')->default(false); //mestre definirá se é torra pra rota do café ou não pelo app
    $table->text('justification')->nullable(); // Obrigatório se 'wasted' ou troca de mestre
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
        Schema::dropIfExists('roasting_batches');
    }
}
