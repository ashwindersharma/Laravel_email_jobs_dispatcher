<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('campaign_recipients', function (Blueprint $table) {

            $table->id();

            $table->foreignId('campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('contact_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')
                ->default('pending');

            $table->string('provider')
                ->nullable();

            $table->string('provider_message_id')
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->unsignedInteger('retry_count')
                ->default(0);

            $table->timestamp('sent_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'campaign_id',
                'contact_id'
            ]);

            $table->index('status');

            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('campaign_recipients');
    }
};
