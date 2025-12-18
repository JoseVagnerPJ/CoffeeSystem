<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
    Schema::create('samples', function (Blueprint $table) {
        $table->id();
        $table->foreignId('green_coffee_lot_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('partner_id')->nullable()->constrained()->onDelete('set null');
        $table->decimal('moisture', 4, 2)->nullable();
        $table->string('process_type')->nullable();
        $table->text('fermentation')->nullable();
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
        Schema::dropIfExists('samples');
    }
}
