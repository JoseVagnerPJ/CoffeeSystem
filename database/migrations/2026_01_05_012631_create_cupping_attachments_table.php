<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CuppingAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('cupping_attachments', function (Blueprint $table) {
        $table->id();
        // Pode ser anexo da MESA ou de um ITEM específico
        $table->morphs('attachable'); 
        $table->string('file_name');
        $table->string('file_path');
        $table->string('file_type'); // pdf, algt (artisan), jpg
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
        Schema::dropIfExists('cupping_attachments');
    }
}
