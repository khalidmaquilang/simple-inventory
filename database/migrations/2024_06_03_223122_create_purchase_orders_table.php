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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->string('purchase_code')->unique()->index();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['pending', 'received', 'partially_received', 'cancelled']);
            $table->decimal('total_amount');
            $table->decimal('paid_amount');
            $table->foreignId('supplier_id');
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
        Schema::dropIfExists('purchase_orders');
    }
};
