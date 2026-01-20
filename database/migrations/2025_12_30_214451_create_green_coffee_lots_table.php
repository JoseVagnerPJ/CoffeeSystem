<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenCoffeeLotsTable extends Migration
{
    public function up()
    {
        Schema::create('green_coffee_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Relacionamento com Perfil Sensorial (Permite a cor do grão)
            $table->foreignId('sensory_profile_id')->nullable()->constrained()->onDelete('set null');

            // Relacionamento Polimórfico (ID e Tipo do Fornecedor/Fazenda/Empresa)
            $table->unsignedBigInteger('origin_id')->nullable();
            $table->string('origin_type')->nullable();

            $table->string('reference');       
            $table->string('origin'); // Mantido para nome de exibição rápido          
            $table->string('process')->nullable(); 
            $table->integer('quantity_bags');
            $table->decimal('unit_weight', 8, 2);  
            $table->decimal('total_weight', 10, 2); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('green_coffee_lots');
    }
}