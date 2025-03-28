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
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->unsigned()->index()->nullable();
            $table->char('type', 10)->index();
            $table->enum('group', ['general', 'customer', 'creator']);
            $table->text('text');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('faq')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq');
    }
};
