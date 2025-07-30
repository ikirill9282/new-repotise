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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned()->index();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->decimal('price');
            $table->decimal('old_price')->nullable();
            $table->decimal('discount')->unsigned()->nullable()->default(0);
            $table->integer('count')->unsigned();
            $table->decimal('total');
            $table->decimal('total_without_discount')->unsigned()->nullable();
            $table->decimal('payment_fee')->nullable();
            $table->decimal('seller_reward')->nullable();
            $table->decimal('referal_reward')->nullable();
            $table->decimal('platform_reward')->nullable();
            $table->tinyInteger('refunded')->default(0);

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
