<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('current_state')->default('IDLE');
            $table->foreignId('active_ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->json('state_payload')->nullable();
            $table->timestamp('last_inbound_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('wa_message_id')->unique()->index();
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->string('sender_role')->default('customer');
            $table->enum('direction', ['inbound', 'outbound'])->default('inbound');
            $table->string('message_type')->default('text');
            $table->text('body')->nullable();
            $table->string('media_id')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('bot_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->text('raw_query')->nullable();
            $table->string('intent_detected')->nullable();
            $table->float('confidence_score')->nullable();
            $table->foreignId('matched_faq_id')->nullable()->constrained('faqs')->nullOnDelete();
            $table->string('outcome')->default('answered');
            $table->integer('response_time_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_events');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
