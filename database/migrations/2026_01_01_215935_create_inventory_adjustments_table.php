<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->foreignId('inventory_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
            
            $table->integer('closed_bags')->default(0); 
            $table->decimal('bag_weight', 8, 2)->default(60); 
            $table->decimal('remainder_weight', 8, 2)->default(0); 
            
            $table->decimal('system_weight', 12, 2);
            $table->decimal('physical_weight', 12, 2);
            $table->decimal('difference_kg', 12, 2);

            $table->string('new_address_row', 5)->nullable();
            $table->string('new_address_pos', 5)->nullable();
            
            $table->text('reason')->nullable(); 
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
        Schema::dropIfExists('inventory_adjustments');
    }
}