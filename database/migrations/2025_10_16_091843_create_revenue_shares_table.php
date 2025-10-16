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
        Schema::create('revenue_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('referrer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('subscription_id')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('stripe_fee', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('author_amount', 10, 2);
            $table->decimal('referral_amount', 10, 2)->default(0);
            $table->decimal('service_amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index('subscription_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_shares');
    }
};
