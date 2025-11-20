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
        // Добавляем статус Spam в таблицу statuses
        \App\Models\Status::firstOrCreate(
            ['title' => 'Spam'],
            ['title' => 'Spam']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Status::where('title', 'Spam')->delete();
    }
};
