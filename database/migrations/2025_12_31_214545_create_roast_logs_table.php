<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
    Schema::create('roast_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        $table->foreignId('roast_demand_id')->constrained()->onDelete('cascade');
        $table->foreignId('mestre_id')->constrained('users');
        $table->decimal('weight_in', 7, 2);
        $table->decimal('weight_out', 7, 2);
        $table->dateTime('roast_date');
        $table->text('observations')->nullable();
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
        Schema::dropIfExists('roast_logs');
    }
}