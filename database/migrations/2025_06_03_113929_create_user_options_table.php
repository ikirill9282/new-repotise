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
        Schema::create('user_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index()->unique();
            $table->bigInteger('level_id')->unsigned()->default(1);

            $table->decimal('fee')->nullable();
            $table->decimal('sales_treshold')->nullable();
            $table->decimal('space')->nullable();

            $table->string('avatar')->default('/storage/images/default_avatar.svg');
            $table->text('description')->nullable();

            $table->string('full_name')->nullable();
            $table->string('street')->nullable();
            $table->string('street2')->nullable();
            $table->string('city')->nullable();
            $table->integer('zip')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('birthday')->nullable();
            $table->integer('tax_id')->nullable();
            $table->string('phone')->nullable();

            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('google')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_options');
    }
};
