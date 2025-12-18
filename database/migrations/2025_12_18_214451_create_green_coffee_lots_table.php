<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenCoffeeLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
    Schema::create('green_coffee_lots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('partner_id')->constrained()->onDelete('restrict');
        $table->string('variety');
        $table->string('crop_year', 10);
        $table->decimal('bag_weight', 5, 2);
        $table->integer('total_bags');
        $table->string('map_coordinates')->nullable();
        $table->text('general_notes')->nullable();
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
        Schema::dropIfExists('green_coffee_lots');
    }
}
