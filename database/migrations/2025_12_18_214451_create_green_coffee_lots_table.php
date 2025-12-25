<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenCoffeeLotsTable extends Migration
{
    public function up()
    {
        Schema::create('green_coffee_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('reference');       
            $table->string('origin');          
            $table->string('process');         
            
            $table->string('address_row');     
            $table->integer('address_pos');    
            
            $table->integer('quantity_bags');
            $table->decimal('unit_weight', 8, 2);  
            $table->decimal('total_weight', 10, 2); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('green_coffee_lots');
    }
}