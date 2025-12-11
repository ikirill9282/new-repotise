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
        Schema::create('ip_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('action')->index()->comment('login, register, reset_password, reset_2fa, show_contacts, report_comment, report_article');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->bigInteger('related_id')->unsigned()->nullable()->index()->comment('ID связанной сущности (комментарий, статья и т.д.)');
            $table->timestamp('attempted_at')->index();
            $table->boolean('success')->default(false);
            $table->timestamps();
            
            $table->index(['ip_address', 'action', 'attempted_at']);
            $table->index(['user_id', 'action', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_attempts');
    }
};
