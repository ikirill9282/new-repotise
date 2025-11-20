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
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'type')) {
                $table->string('type')->nullable()->index()->after('user_id')->comment('complaint or content_error');
            }
            if (!Schema::hasColumn('reports', 'reason')) {
                $table->string('reason')->nullable()->after('type')->comment('Spam or Scam, Offensive or abusive, Inappropriate content');
            }
            if (!Schema::hasColumn('reports', 'resolution_type')) {
                $table->string('resolution_type')->nullable()->after('status')->comment('action_taken or dismissed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('reports', 'type')) {
                $columnsToDrop[] = 'type';
            }
            if (Schema::hasColumn('reports', 'reason')) {
                $columnsToDrop[] = 'reason';
            }
            if (Schema::hasColumn('reports', 'resolution_type')) {
                $columnsToDrop[] = 'resolution_type';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
