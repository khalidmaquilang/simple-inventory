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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('inventory_id');
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->integer('quantity_before_adjustment');
            $table->integer('quantity');
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'return']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
