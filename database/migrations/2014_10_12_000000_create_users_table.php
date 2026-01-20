<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
    $table->foreignId('profile_id')->nullable()->constrained('profiles')->onDelete('set null');
    $table->string('name');
    
    // REMOVA: $table->string('username')->unique();
    // USE ESTA LINHA:
    $table->string('username'); 

    $table->string('password');
    $table->boolean('is_active')->default(true);
    $table->string('photo')->nullable();
    $table->rememberToken();
    $table->timestamps();

    // ADICIONE ESTA CHAVE COMPOSTA:
    $table->unique(['username', 'main_company_id']); 
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}