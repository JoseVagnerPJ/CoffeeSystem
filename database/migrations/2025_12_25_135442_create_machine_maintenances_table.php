<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    Schema::create('machine_maintenances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('machine_id')->constrained()->onDelete('cascade');
        $table->date('maintenance_date');
        $table->string('provider'); // Técnico ou Empresa
        $table->text('description');
        $table->date('next_maintenance_date')->nullable();
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
        Schema::dropIfExists('machine_maintenances');
    }
}
