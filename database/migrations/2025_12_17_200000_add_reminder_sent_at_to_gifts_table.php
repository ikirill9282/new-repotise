<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            if (Schema::hasColumn('gifts', 'reminder_sent_at')) {
                return;
            }

            $table->timestamp('reminder_sent_at')->nullable()->after('claimed_at');
        });
    }

    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            if (!Schema::hasColumn('gifts', 'reminder_sent_at')) {
                return;
            }

            $table->dropColumn('reminder_sent_at');
        });
    }
};

