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
            
            // Provador: Nullable para permitir provas via QR-Code (Visitantes)
            $table->foreignId('user_id')->nullable()->constrained(); 

            // --- DADOS DE VISITANTE (CAMPEONATOS / QR-CODE) ---
            $table->string('visitor_name')->nullable();
            $table->string('visitor_cpf')->nullable();

            // --- NÍVEL DE TORRA ---
            $table->integer('roast_level')->default(3); // 1 a 5 (Very Light a Dark)

            // --- OS 10 ATRIBUTOS POSITIVOS (Notas 0 a 10) ---
            $table->decimal('fragrance_aroma', 4, 2)->default(0);
            $table->decimal('flavor', 4, 2)->default(0);
            $table->decimal('aftertaste', 4, 2)->default(0);
            $table->decimal('acidity', 4, 2)->default(0);
            $table->decimal('body', 4, 2)->default(0);
            $table->decimal('balance', 4, 2)->default(0);
            $table->decimal('uniformity', 4, 2)->default(10.00); // Padrão SCA inicia cheio
            $table->decimal('clean_cup', 4, 2)->default(10.00);  // Padrão SCA inicia cheio
            $table->decimal('sweetness', 4, 2)->default(10.00);  // Padrão SCA inicia cheio
            $table->decimal('overall', 4, 2)->default(0);

            // --- ESCALAS DE INTENSIDADE / SUB-ATRIBUTOS (Os "risquinhos" da ficha) ---
            // Usamos inteiros para representar a posição visual nas réguas da SCA
            $table->decimal('fragrance_dry', 4, 2)->default(0);     // Intensidade Seco
            $table->decimal('fragrance_break', 4, 2)->default(0);   // Intensidade Quebra
            $table->decimal('acidity_intensity', 4, 2)->default(0); // Escala Low-High
            $table->decimal('body_level', 4, 2)->default(0);        // Escala Thin-Heavy

            // --- DEFEITOS (ATRIBUTO NEGATIVO) ---
            $table->integer('defect_cups')->default(0);      // # de xícaras com defeito
            $table->integer('defect_intensity')->default(0); // 2 (Taint) ou 4 (Fault)
            $table->decimal('defects', 4, 2)->default(0);    // Cálculo: cups * intensity

            // --- PONTUAÇÃO FINAL ---
            // Cálculo SCA: (Soma dos 10 positivos) - Defeitos
            $table->decimal('final_score', 5, 2)->default(0.00);

            // --- STATUS E OBSERVAÇÕES ---
            $table->boolean('is_invalid')->default(false);
            $table->string('invalid_reason')->nullable(); 
            $table->text('observations')->nullable(); 

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