<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('cup_tag', function (Blueprint $table) {
        // ID da empresa para isolamento multi-tenant
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        
        $table->foreignId('cup_id')->constrained()->onDelete('cascade');
        $table->foreignId('tag_id')->constrained()->onDelete('cascade');

        // Opcional: Índice para acelerar as buscas de tags por avaliação
        $table->index(['cup_id', 'tag_id', 'main_company_id']);
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cup_tag');
    }
}