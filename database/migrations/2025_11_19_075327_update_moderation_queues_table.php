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
        Schema::table('moderation_queues', function (Blueprint $table) {
            if (!Schema::hasColumn('moderation_queues', 'model')) {
                $table->string('model')->after('priority');
            }
            if (!Schema::hasColumn('moderation_queues', 'model_id')) {
                $table->bigInteger('model_id')->after('model');
            }
            // Добавляем индексы для быстрого поиска
            if (Schema::hasColumn('moderation_queues', 'model') && Schema::hasColumn('moderation_queues', 'model_id')) {
                $table->index(['model', 'model_id']);
            }
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moderation_queues', function (Blueprint $table) {
            if (Schema::hasColumn('moderation_queues', 'model')) {
                $table->dropIndex(['model', 'model_id']);
            }
            if (Schema::hasColumn('moderation_queues', 'priority')) {
                $table->dropIndex(['priority']);
            }
        });
    }
};
