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
        Schema::create('user_referals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner_id')->unsigned();
            $table->bigInteger('referal_id')->unsigned()->unique();

            $table->unique(['owner_id', 'referal_id']);
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referal_id')->references('id')->on('users');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_referals');
    }
};
