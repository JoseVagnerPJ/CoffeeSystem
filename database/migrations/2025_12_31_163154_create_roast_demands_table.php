<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastDemandsTable extends Migration
{
public function up() {
    Schema::create('roast_demands', function (Blueprint $table) {
        $table->id();
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        $table->foreignId('user_id')->constrained();
        $table->foreignId('green_coffee_lot_id')->constrained();
        $table->foreignId('sensory_profile_id')->nullable()->constrained();
        $table->foreignId('machine_id')->constrained();
        $table->foreignId('mestre_user_id')->constrained('users'); 
        
        $table->string('reference')->unique();
        $table->decimal('total_weight_requested', 10, 2); 
        $table->decimal('partial_weight', 10, 2);
        $table->string('roast_level'); 
        $table->text('notes')->nullable();
        
        $table->string('status')->default('pending');
        $table->integer('position')->default(0); 
        
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('roast_demands');
    }
}