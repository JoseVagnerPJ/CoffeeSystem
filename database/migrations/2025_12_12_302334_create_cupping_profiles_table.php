<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cupping_profiles', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento Multi-empresa
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            
            // Identificação do Perfil
            $table->string('name'); // Ex: Protocolo SCA Completo, Perfil Comercial, Cup of Excellence
            $table->text('description')->nullable();

            /**
             * ATRIBUTOS PRINCIPAIS (0 a 10)
             * Define se o atributo aparecerá para avaliação. 
             * Se for 'false', o sistema assumirá nota 10.00 automaticamente.
             */
            $table->boolean('eval_fragrance')->default(true);
            $table->boolean('eval_flavor')->default(true);
            $table->boolean('eval_aftertaste')->default(true);
            $table->boolean('eval_acidity')->default(true);
            $table->boolean('eval_body')->default(true);
            $table->boolean('eval_balance')->default(true);
            $table->boolean('eval_uniformity')->default(true);
            $table->boolean('eval_clean_cup')->default(true);
            $table->boolean('eval_sweetness')->default(true);
            $table->boolean('eval_overall')->default(true);
            $table->boolean('eval_defects')->default(true); // O atributo negativo

            /**
             * SUB-ATRIBUTOS E ESCALAS TÉCNICAS (Ficha SCA)
             * Define se os "risquinhos" e seletores extras aparecem.
             */
            $table->boolean('show_roast_level')->default(true);      // Nível de torra (escala visual)
            $table->boolean('show_fragrance_scales')->default(true); // Escalas Dry e Break (Aroma)
            $table->boolean('show_intensity_scales')->default(true); // Intensidade de Acidez e Corpo (Low/High)
            $table->boolean('show_defect_details')->default(true);   // Cálculo detalhado (# xic x intens)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupping_profiles');
    }
};