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
    public function up()
{
    Schema::create('roasting_batches', function (Blueprint $table) {
        $table->id();
        // Vínculo com o lote de café verde
        $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
        // Vínculo com a máquina (certifique-se de que a tabela 'machines' existe)
        $table->foreignId('machine_id')->constrained();
        
        $table->decimal('input_weight', 8, 2); // Peso de entrada (café verde)
        $table->decimal('output_weight', 8, 2)->nullable(); // Peso de saída (café torrado)
        $table->string('status')->default('completed'); // 'planned', 'roasting', 'completed'
        
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
