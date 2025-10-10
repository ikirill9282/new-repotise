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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->from(100200);
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('status_id')->unsigned()->index();
            $table->decimal('cost', 10, 2);
            $table->decimal('discount_amount', 10, 2)->nullable()->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('cost_without_discount', 10, 2);
            $table->decimal('cost_without_tax', 10, 2);
            $table->tinyInteger('sub')->default(0);
            $table->string('sub_period')->nullable();
            $table->decimal('stripe_fee')->nullable();
            $table->decimal('base_reward')->nullable();
            $table->decimal('seller_reward')->nullable();
            $table->decimal('referal_reward')->nullable();
            $table->decimal('platform_reward')->nullable();
            $table->bigInteger('discount_id')->unsigned()->nullable()->index();
            $table->string('payment_id')->nullable()->unique();
            $table->tinyInteger('gift')->default(0);
            $table->string('recipient')->index()->nullable();
            $table->string('recipient_message')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
