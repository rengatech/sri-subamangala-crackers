<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('wa_message_id')->nullable()->index();
            $table->string('phone_number')->index();
            $table->string('contact_name')->nullable();
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->enum('type', ['text', 'template', 'image', 'document', 'video', 'audio'])->default('text');
            $table->text('body')->nullable();
            $table->string('template_name')->nullable();
            $table->string('media_url')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed', 'received'])->default('sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
