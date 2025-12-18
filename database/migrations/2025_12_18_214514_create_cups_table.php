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
    public function up(): void {
    Schema::create('cups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cupping_session_id')->constrained()->onDelete('cascade');
        $table->foreignId('sample_id')->constrained()->onDelete('restrict');
        $table->integer('cup_number');
        $table->enum('roast_level', ['clara', 'media-clara', 'media', 'media-escura', 'escura']);
        
        // Atributos SCA
        $attributes = ['fragrance', 'flavor', 'aftertaste', 'acidity', 'body', 'balance', 'sweetness', 'clean_cup', 'uniformity', 'overall', 'total_score'];
        foreach ($attributes as $attr) {
            $table->decimal($attr, 5, 2)->default(0);
        }

        $table->text('sensory_observations')->nullable();
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
