<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplesTable extends Migration
{
    public function up(): void 
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            // Vinculação ao Lote de Café
            $table->foreignId('green_coffee_lot_id')->nullable()->constrained()->onDelete('set null');
            
            $table->unsignedBigInteger('producer_id')->nullable();
            
            $table->decimal('moisture', 4, 2)->nullable();
            $table->string('process_type')->nullable();
            $table->text('fermentation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('samples');
    }
}