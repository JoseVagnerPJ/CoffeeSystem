<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            // Isolamento por terminal/empresa
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            
            $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('green_coffee_lot_locations');
            $table->foreignId('user_id')->constrained(); // Quem realizou a movimentação
            
            $table->string('type'); // 'in', 'out', 'adjustment'
            $table->decimal('weight', 10, 2);
            $table->string('reason');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('stock_movements');
    }
}