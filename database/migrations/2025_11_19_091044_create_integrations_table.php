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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index()->comment('stripe, mailgun, mailtrap, ga4, etc.');
            $table->string('type')->index()->comment('payment, email, analytics, other');
            $table->string('status')->default('not_configured')->index()->comment('active, inactive, not_configured');
            $table->json('config')->nullable()->comment('JSON with API keys, secrets, and other parameters');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
