<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('carriers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained()->onDelete('cascade');
        $table->string('name'); // Nome Fantasia
        $table->string('omie_name'); // Nome EXATO como você escreve no Omie
        $table->string('api_type')->default('fixed'); // 'api' ou 'fixed' (tabela)
        $table->text('api_token')->nullable(); // Chave de acesso da transportadora
        $table->text('api_user')->nullable();
        $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('carriers');
    }
}
