<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensoryProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('sensory_profiles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->boolean('is_blend')->default(false);
        $table->string('roast_intensity'); // Ex: Clara, Média, Escura
        $table->string('color_hex', 7)->default('#3d2b1f');
        $table->text('notes')->nullable(); // Guardará as #tags
        
        // Atributos de 1 a 5
        $table->integer('sweetness')->default(1);
        $table->integer('acidity')->default(1);
        $table->integer('body')->default(1);
        $table->integer('bitterness')->default(1);
        
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
        Schema::dropIfExists('sensory_profiles');
    }
}
