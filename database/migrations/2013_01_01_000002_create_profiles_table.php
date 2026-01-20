<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // Linha adicionada para vincular o perfil à empresa principal
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->string('name');
            $table->json('permissions')->nullable(); // Guardará ['edit_suppliers' => true, ...]
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
        Schema::dropIfExists('profiles');
    }
}