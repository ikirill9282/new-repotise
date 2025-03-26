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
        Schema::create('section_variables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id')->unsigned()->index();
            $table->string('name');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'name']);
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_variables');
    }
};
