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
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->string('stripe_refund_id')
                ->nullable()
                ->after('status');
            $table->decimal('refund_amount', 10, 2)
                ->nullable()
                ->after('details');
            $table->string('refund_currency', 3)
                ->nullable()
                ->after('refund_amount');
            $table->string('stripe_refund_status')
                ->nullable()
                ->after('refund_currency');
            $table->text('stripe_refund_error')
                ->nullable()
                ->after('stripe_refund_status');
        });

        Schema::table('revenue_shares', function (Blueprint $table) {
            $table->foreignId('refund_request_id')
                ->nullable()
                ->after('order_id')
                ->constrained('refund_requests')
                ->nullOnDelete();
            $table->timestamp('refunded_at')
                ->nullable()
                ->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_refund_id',
                'refund_amount',
                'refund_currency',
                'stripe_refund_status',
                'stripe_refund_error',
            ]);
        });

        Schema::table('revenue_shares', function (Blueprint $table) {
            $table->dropConstrainedForeignId('refund_request_id');
            $table->dropColumn('refunded_at');
        });
    }
};
