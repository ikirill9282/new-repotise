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
        Schema::create('discounts', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('author_id')->default(0);
          $table->bigInteger('user_id')->unsigned()->index()->nullable();
          $table->enum('visibility', ['private', 'public'])->default('public');
          $table->string('group')->nullable()->index();
          $table->string('type');
          $table->string('target');
          $table->integer('target_id')->nullable();
          $table->string('code')->unique();
          $table->integer('percent')->nullable();
          $table->decimal('sum')->nullable();
          $table->decimal('min')->nullable();
          $table->decimal('max')->nullable();
          $table->integer('uses')->unsigned()->default(1);
          $table->tinyInteger('one_time')->default(1);
          $table->tinyInteger('active')->default(1)->index();
          $table->datetime('end');

          $table->timestamps();

          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
