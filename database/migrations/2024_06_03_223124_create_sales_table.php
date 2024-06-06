<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->date('sale_date');
            $table->double('vat');
            $table->decimal('discount');
            $table->enum('discount_type', \App\Enums\DiscountTypeEnum::toArray());
            $table->decimal('total_amount');
            $table->decimal('paid_amount');
            $table->text('notes')->nullable();
            $table->foreignId('customer_id');
            $table->foreignId('payment_type_id');
            $table->foreignId('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
