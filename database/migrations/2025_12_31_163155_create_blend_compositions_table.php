<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlendCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blend_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensory_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('green_coffee_lot_id')->constrained(); // O café que compõe
            $table->decimal('percentage', 5, 2); // Ex: 60.00
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
        Schema::dropIfExists('blend_compositions');
    }
}
