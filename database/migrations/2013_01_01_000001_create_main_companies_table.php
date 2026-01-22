<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('main_companies', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('cnpj')->unique();
        $table->string('logo')->nullable();
        $table->boolean('is_active')->default(true);
        $table->string('map_view')->default('default');
        
        // --- NOVOS CAMPOS PARA O PORTAL MASTER ---
        // Controle de Módulos (Booleanos)
        $table->boolean('mod_lab')->default(false);
        $table->boolean('mod_stock')->default(false);
        $table->boolean('mod_roast')->default(false);
        $table->boolean('mod_prod')->default(false);
        
        // Cache do último pagamento para performance na listagem
        $table->date('last_payment_date')->nullable();
        
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
        Schema::dropIfExists('main_companies');
    }
}