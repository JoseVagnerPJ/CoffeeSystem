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
public function up() {
    Schema::create('cups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cupping_item_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained(); // O provador
        
        // Atributos (0 a 10)
        $table->decimal('acidity', 4, 2)->default(0);
        $table->decimal('body', 4, 2)->default(0);
        $table->decimal('sweetness', 4, 2)->default(0);
        $table->decimal('aftertaste', 4, 2)->default(0);
        
        // Soma: 60 (Base) + Atributos
        $table->decimal('final_score', 5, 2)->default(60.00);

        // Inutilização
        $table->boolean('is_invalid')->default(false);
        $table->text('invalid_reason')->nullable();
        
        $table->text('observations')->nullable(); // Comentários livres
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
