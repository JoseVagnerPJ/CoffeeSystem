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
        $table->string('name');
        $table->morphs('origin'); 
        $table->date('receipt_date');
        $table->string('supplier_batch')->nullable();
        $table->string('variety');
        $table->string('drying_process');
        $table->string('fermentation')->nullable();
        $table->decimal('humidity', 5, 2)->nullable();
        $table->string('screen')->nullable();
        $table->integer('altitude')->nullable();
        $table->string('crop')->nullable();
        $table->text('observations')->nullable();
        $table->boolean('is_active')->default(true); 
        
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('samples');
    }
}