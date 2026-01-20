<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOriginLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('origin_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        $table->foreignId('producer_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('farm_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('origin_links');
    }
}