<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng contacts - lưu trữ tin nhắn liên hệ từ khách thăm.
     * Hỗ trợ các trạng thái: unread, read, replied, archived, spam.
     */
    public function up(): void
    {
        Schema::create('portfolio_contacts', function (Blueprint $table) {
            $table->id();

            // Sender info
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();

            // Message
            $table->string('subject');
            $table->text('message');

            // Status management
            $table->enum('status', ['unread', 'read', 'replied', 'archived', 'spam'])
                  ->default('unread');

            // Meta
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('source')->nullable();           // Where the form came from

            // Admin reply
            $table->text('admin_notes')->nullable();
            $table->timestamp('replied_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_contacts');
    }
};
