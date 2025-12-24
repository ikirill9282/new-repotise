<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Guard for environments where column already exists
            if (Schema::hasColumn('products', 'show_on_creator_page')) {
                return;
            }

            $table->boolean('show_on_creator_page')
                ->default(true)
                ->index()
                ->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'show_on_creator_page')) {
                return;
            }

            $table->dropIndex(['show_on_creator_page']);
            $table->dropColumn('show_on_creator_page');
        });
    }
};

