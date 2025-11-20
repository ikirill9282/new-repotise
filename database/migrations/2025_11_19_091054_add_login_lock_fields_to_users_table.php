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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'login_locked_until')) {
                $table->timestamp('login_locked_until')->nullable()->after('deleted_at')->index()->comment('Timestamp until which login is locked due to failed attempts');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('login_locked_until')->comment('Count of consecutive failed login attempts');
            }
            if (!Schema::hasColumn('users', 'last_failed_login_at')) {
                $table->timestamp('last_failed_login_at')->nullable()->after('failed_login_attempts')->comment('Timestamp of last failed login attempt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'login_locked_until')) {
                $columnsToDrop[] = 'login_locked_until';
            }
            if (Schema::hasColumn('users', 'failed_login_attempts')) {
                $columnsToDrop[] = 'failed_login_attempts';
            }
            if (Schema::hasColumn('users', 'last_failed_login_at')) {
                $columnsToDrop[] = 'last_failed_login_at';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
