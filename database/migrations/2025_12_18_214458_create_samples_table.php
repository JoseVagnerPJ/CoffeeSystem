<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplesTable extends Migration
{
   public function up(): void
{
    Schema::create('samples', function (Blueprint $table) {
        $table->id(); // Lote Interno
        $table->string('name');
        
        // Esta linha cria dois campos: origin_id e origin_type
        // O origin_type guardará se é "App\Models\Company" ou "App\Models\Producer"
        $table->morphs('origin'); 

        $table->date('receipt_date');
        $table->string('supplier_batch');
        $table->string('variety');
        $table->string('drying_process');
        $table->string('fermentation')->nullable();
        $table->decimal('humidity', 5, 2)->nullable();
        $table->string('screen')->nullable();
        $table->integer('altitude')->nullable();
        $table->string('crop')->nullable();
        $table->text('observations')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('samples');
    }
}