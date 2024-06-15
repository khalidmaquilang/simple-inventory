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
        Schema::create('goods_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->foreignId('sale_id')->nullable()->index();
            $table->foreignId('user_id')->index();
            $table->foreignId('customer_id')->nullable()->index();
            $table->foreignId('supplier_id')->nullable()->index();
            $table->string('gin_code')->unique();
            $table->date('issue_date');
            $table->string('sku');
            $table->string('name');
            $table->integer('quantity');
            $table->foreignId('product_id');
            $table->enum('type', ['sale', 'transfer', 'write_off', 'return_to_supplier']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_issues');
    }
};
