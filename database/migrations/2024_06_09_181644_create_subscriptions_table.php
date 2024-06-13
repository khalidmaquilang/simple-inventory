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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->index();
            $table->foreignId('plan_id')->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'trialing', 'canceled', 'past_due', 'unpaid']);
            $table->integer('extra_users')->default(0);
            $table->decimal('total_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
