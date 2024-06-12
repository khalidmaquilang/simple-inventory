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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price');
            $table->string('billing_cycle');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('max_users')->default(1);
            $table->integer('max_roles')->default(1);
            $table->integer('max_monthly_purchase_order')->default(10);
            $table->integer('max_monthly_sale_order')->default(10);
            $table->enum('type', ['standard', 'custom'])->default('standard');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
