<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastDemandsTable extends Migration
{
public function up() {
    Schema::create('roast_demands', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->foreignId('green_coffee_lot_id')->constrained();
        $table->foreignId('sensory_profile_id')->nullable()->constrained(); // Adicionado
        $table->foreignId('machine_id')->constrained();
        $table->foreignId('mestre_user_id')->constrained('users'); 
        
        $table->string('reference')->unique();
        $table->decimal('total_weight_requested', 10, 2); 
        $table->decimal('partial_weight', 10, 2); // Peso por ciclo
        $table->string('roast_level'); 
        $table->text('notes')->nullable(); // Adicionado
        
        $table->string('status')->default('pending');
        $table->boolean('is_special_tour')->default(false);
        $table->timestamps();
    });

}

    public function down()
    {
        Schema::dropIfExists('roast_demands');
    }
}