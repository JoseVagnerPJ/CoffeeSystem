<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
    Schema::create('roast_demands', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained('roasting_machines');
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roast_demands');
    }
}
