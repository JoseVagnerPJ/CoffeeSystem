<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenCoffeeLotLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up() {
    Schema::create('green_coffee_lot_locations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
        $table->string('address_row');
        $table->integer('address_pos');
        $table->integer('quantity_bags'); // Sacas cheias
        $table->decimal('remainder_weight', 8, 2)->default(0); // Peso da saca quebrada
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
        Schema::dropIfExists('green_coffee_lot_locations');
    }
}
