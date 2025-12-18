<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastingMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
    Schema::create('roasting_machines', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('max_capacity', 7, 2);
        $table->integer('maintenance_limit')->default(40);
        $table->integer('current_cycles')->default(0);
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
        Schema::dropIfExists('roasting_machines');
    }
}
