<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('lot_attachments', function (Blueprint $table) {
            $table->id();
            // Vincula o anexo ao lote e apaga os anexos se o lote for excluído
            $table->foreignId('green_coffee_lot_id')->constrained()->onDelete('cascade');
            $table->string('file_path'); // Caminho da pasta storage
            $table->string('file_name'); // Nome original do arquivo (ex: laudo.pdf)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lot_attachments');
    }
}