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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Pengirim pesan (User)
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            // Penerima pesan (User)
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            // Isi pesan teks
            $table->text('message');
            // Status apakah pesan sudah dibaca
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
