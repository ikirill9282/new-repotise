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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_gift_order')->default(false)->after('gift');
            $table->bigInteger('buyer_user_id')->unsigned()->nullable()->index()->after('user_id');
            
            $table->foreign('buyer_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['buyer_user_id']);
            $table->dropColumn(['is_gift_order', 'buyer_user_id']);
        });
    }
};
