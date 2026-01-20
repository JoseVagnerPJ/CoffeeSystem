<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('company_payments', function (Blueprint $table) {
        $table->id();
        // Chave estrangeira ligando à empresa
        $table->foreignId('main_company_id')->constrained('main_companies')->onDelete('cascade');
        
        $table->decimal('amount', 10, 2); // Valor do pagamento
        $table->date('payment_date');    // Data em que foi pago
        $table->string('payment_method'); // Pix, Boleto, etc
        $table->text('notes')->nullable(); // Observações do modal
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
        Schema::dropIfExists('company_payments');
    }
}