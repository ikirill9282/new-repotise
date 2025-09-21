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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('status_id')->unsigned()->index()->default(3);
            $table->string('title');
            // $table->string('subtitle')->nullable();
            $table->text('slug');
            $table->integer('views')->default(0);
            // $table->text('annotation')->nullable();
            $table->longText('text');

            $table->string('seo_title')->nullable();
            $table->text('seo_text')->nullable();

            $table->datetime('scheduled_at')->index()->nullable();
            $table->datetime('published_at')->index()->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
