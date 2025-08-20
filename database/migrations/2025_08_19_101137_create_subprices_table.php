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
        Schema::create('subprices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->unique();
            $table->decimal('month');
            $table->decimal('quarter');
            $table->decimal('year');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subprices');
    }
};
