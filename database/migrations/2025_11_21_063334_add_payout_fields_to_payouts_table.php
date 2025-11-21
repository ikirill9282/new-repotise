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
        Schema::table('payouts', function (Blueprint $table) {
            // Change status from enum to string to support new statuses
            $table->string('status', 20)->default('pending')->change();
            
            // Add new fields
            $table->string('payout_id', 50)->unique()->nullable()->after('id');
            $table->string('payout_method', 100)->nullable()->after('stripe_payout_id');
            $table->decimal('fees', 10, 2)->default(0)->after('amount');
            $table->decimal('total_deducted', 10, 2)->nullable()->after('fees');
            
            // Add index for payout_id lookup
            $table->index('payout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn(['payout_id', 'payout_method', 'fees', 'total_deducted']);
            // Note: Reverting status enum change would require manual intervention
        });
    }
};
