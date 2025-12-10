<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Используем прямой SQL для изменения типа колонки
        DB::statement('ALTER TABLE `user_options` MODIFY `tax_id` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно к integer (но это может привести к потере данных для длинных значений)
        DB::statement('ALTER TABLE `user_options` MODIFY `tax_id` INT NULL');
    }
};
