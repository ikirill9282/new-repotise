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
        Schema::table('revenue_shares', function (Blueprint $table) {
            $table->unsignedBigInteger('payout_id')->nullable()->after('order_id');
            $table->index('payout_id');
        });
        
        // Add foreign key constraint after payouts table is created
        // This will be done in a separate migration that runs after create_payouts_table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenue_shares', function (Blueprint $table) {
            $table->dropIndex(['payout_id']);
            $table->dropColumn('payout_id');
        });
    }
};
