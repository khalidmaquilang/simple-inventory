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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('grn_code')->unique()->index();
            $table->foreignId('purchase_order_id')->index();
            $table->foreignId('user_id')->index();
            $table->date('received_date');
            $table->string('sku');
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('unit_cost');
            $table->foreignId('product_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
