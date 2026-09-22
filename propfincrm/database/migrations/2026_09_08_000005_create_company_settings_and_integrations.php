<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('provider');
            $table->string('name');
            $table->json('credentials');
            $table->json('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('sync_frequency')->default('hourly');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'provider', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_integrations');
    }
};
