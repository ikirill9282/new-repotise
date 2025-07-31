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
          $table->string('title');
          $table->string('slug');
          $table->decimal('price', 10);
          $table->decimal('old_price', 10)->nullable();
          $table->tinyInteger('subscription')->default(0);
          $table->bigInteger('type_id')->unsigned()->index();
          $table->bigInteger('location_id')->unsigned()->index();
          $table->bigInteger('status_id')->unsigned()->index()->default(3);
          $table->float('rating')->default(0);
          $table->integer('refund_policy')->default(90);
          $table->longText('text');
          $table->datetime('published_at')->index()->nullable();
          $table->timestamps();

          $table->foreign('status_id')->references('id')->on('statuses');
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
