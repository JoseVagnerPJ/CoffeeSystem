<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCompaniesTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('companies', function (Blueprint $table) {
        // Adiciona 'action_type' se não existir
        if (!Schema::hasColumn('companies', 'action_type')) {
            $table->string('action_type')->nullable()->after('name');
        }
        
        // Adiciona 'contact' se não existir
        if (!Schema::hasColumn('companies', 'contact')) {
            $table->string('contact')->nullable()->after('cnpj');
        }

        // Garante que os campos de localização existam
        if (!Schema::hasColumn('companies', 'is_international')) {
            $table->boolean('is_international')->default(false);
        }
        if (!Schema::hasColumn('companies', 'city')) {
            $table->string('city')->nullable();
        }
        if (!Schema::hasColumn('companies', 'state')) {
            $table->string('state')->nullable();
        }
        if (!Schema::hasColumn('companies', 'country')) {
            $table->string('country')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
}
