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
        Schema::create('products', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('user_id')->unsigned()->index();
          $table->string('title')->unique();
          $table->text('slug');
          $table->decimal('price', 10);
          $table->decimal('old_price', 10);
          $table->bigInteger('type_id')->unsigned()->index();
          $table->bigInteger('location_id')->unsigned()->index();
          $table->float('rating')->default(0);
          $table->integer('reviews')->default(0);
          $table->longText('text');
          $table->timestamps();

          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('type_id')->references('id')->on('types');
          $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
