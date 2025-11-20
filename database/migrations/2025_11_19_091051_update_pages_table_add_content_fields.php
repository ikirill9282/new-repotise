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
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'content')) {
                $table->longText('content')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('pages', 'status')) {
                $table->string('status')->default('draft')->index()->after('content')->comment('draft, published');
            }
            if (!Schema::hasColumn('pages', 'type')) {
                $table->string('type')->default('custom')->index()->after('status')->comment('system, custom');
            }
            if (!Schema::hasColumn('pages', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('pages', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('pages', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable()->after('seo_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('pages', 'content')) {
                $columnsToDrop[] = 'content';
            }
            if (Schema::hasColumn('pages', 'status')) {
                $columnsToDrop[] = 'status';
            }
            if (Schema::hasColumn('pages', 'type')) {
                $columnsToDrop[] = 'type';
            }
            if (Schema::hasColumn('pages', 'seo_title')) {
                $columnsToDrop[] = 'seo_title';
            }
            if (Schema::hasColumn('pages', 'seo_description')) {
                $columnsToDrop[] = 'seo_description';
            }
            if (Schema::hasColumn('pages', 'seo_keywords')) {
                $columnsToDrop[] = 'seo_keywords';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
