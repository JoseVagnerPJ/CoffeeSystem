<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoastingBatchesTable extends Migration
{
    public function up() {
        Schema::create('roasting_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
            $table->foreignId('roast_demand_id')->constrained()->onDelete('cascade');
            $table->foreignId('mestre_user_id')->constrained('users'); 
            
            $table->integer('batch_number'); 
            $table->decimal('weight_in', 8, 2); 
            $table->decimal('weight_out', 8, 2)->nullable(); 
            
            $table->string('brute_group_id')->nullable(); 

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_special_tour')->default(false);
            $table->text('justification')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roasting_batches');
    }
}