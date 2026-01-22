<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        // Relacionamento obrigatório com a empresa principal
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        
        $table->string('name'); // Removido o ->unique() global
        $table->timestamps();

        // Define que o NOME é único apenas DENTRO da mesma empresa
        $table->unique(['name', 'main_company_id']); 
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}