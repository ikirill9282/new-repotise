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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // complaint, moderation, system_error
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->boolean('read')->default(false);
            $table->bigInteger('read_by')->unsigned()->nullable();
            $table->timestamp('read_at')->nullable();
            
            // Связь с сущностью (polymorphic)
            $table->nullableMorphs('notifiable');
            
            // Дополнительные данные в JSON
            $table->json('data')->nullable();
            
            $table->timestamps();
            
            $table->foreign('read_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['type', 'read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
