<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    Schema::create('farms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        $table->string('name');
        $table->decimal('altitude', 8, 2);
        $table->boolean('is_active')->default(true);
        $table->boolean('is_international')->default(false);
        $table->string('city')->nullable();
        $table->string('state', 2)->nullable();
        $table->string('country')->nullable();
        $table->string('district')->nullable();
        
        $table->text('varieties_tags')->nullable();
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
        Schema::dropIfExists('farms');
    }
}