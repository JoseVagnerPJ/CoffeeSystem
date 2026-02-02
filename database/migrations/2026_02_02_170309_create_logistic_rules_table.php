<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('logistic_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained()->onDelete('cascade');
        $table->string('term'); // Ex: "F.C", "F.U", "DEDICADO"
        $table->enum('action', ['round_up', 'exact', 'fixed_value', 'ignore']); 
        $table->decimal('fixed_value', 10, 2)->nullable(); // Para os R$ 50,00 do dedicado
        $table->string('target_field'); // Onde gravar no Omie (frete_valor ou departamento)
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
        Schema::dropIfExists('logistic_rules');
    }
}
