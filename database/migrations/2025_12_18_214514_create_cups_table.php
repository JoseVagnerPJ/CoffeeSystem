<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->foreignId('cupping_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Provador individual

            // Os 4 Pilares (0 a 10 cada = 40 pontos)
            // Usamos decimal(4,2) para permitir notas como 8.25 ou 9.50
            $table->decimal('acidity', 4, 2)->default(0);
            $table->decimal('body', 4, 2)->default(0);
            $table->decimal('sweetness', 4, 2)->default(0);
            $table->decimal('aftertaste', 4, 2)->default(0);
            
            // Cálculo: 60 (Base) + Soma dos 4 pilares
            // O valor máximo será 100.00 (60 + 10 + 10 + 10 + 10)
            $table->decimal('final_score', 5, 2)->default(60.00);

            // Lógica de Inutilização (Tag de Defeito)
            // Lógica de Inutilização (Tag de Defeito)
            $table->boolean('is_invalid')->default(false);
            $table->string('invalid_reason')->nullable(); // Motivo descrito

            $table->text('observations')->nullable(); // Texto livre
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
        Schema::dropIfExists('cups');
    }
}