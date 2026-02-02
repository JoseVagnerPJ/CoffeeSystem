<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticOrderDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('logistic_order_departments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('logistic_order_id')->constrained()->onDelete('cascade');
    $table->string('department_code');
    $table->decimal('percentage', 5, 2);
    $table->decimal('value', 15, 2);
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
        Schema::dropIfExists('logistic_order_departments');
    }
}
