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
        Schema::table('user_options', function (Blueprint $table) {
            $table->string('preferred_payment_method')->nullable()->after('contact2');
            $table->string('preferred_payout_method')->nullable()->after('preferred_payment_method');
            $table->foreignId('return_policy_id')->nullable()->after('preferred_payout_method')->constrained('policies')->nullOnDelete();

            $table->boolean('creator_visible')->default(true)->after('return_policy_id');
            $table->boolean('show_donate')->default(true)->after('creator_visible');
            $table->boolean('show_products')->default(true)->after('show_donate');
            $table->boolean('show_insights')->default(true)->after('show_products');

            $table->json('notification_settings')->nullable()->after('show_insights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_options', function (Blueprint $table) {
            $table->dropForeign(['return_policy_id']);

            $table->dropColumn([
                'preferred_payment_method',
                'preferred_payout_method',
                'return_policy_id',
                'creator_visible',
                'show_donate',
                'show_products',
                'show_insights',
                'notification_settings',
            ]);
        });
    }
};
