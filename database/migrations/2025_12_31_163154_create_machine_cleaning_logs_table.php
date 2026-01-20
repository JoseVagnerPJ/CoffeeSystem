<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('machine_cleaning_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $blueprint->foreignId('machine_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('user_id')->constrained(); // Quem registrou a limpeza
            $blueprint->timestamp('cleaned_at');
            $blueprint->text('notes')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('machine_cleaning_logs');
    }
};