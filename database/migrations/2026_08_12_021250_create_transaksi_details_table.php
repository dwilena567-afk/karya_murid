<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            // Menyambungkan ke ID struk transaksi di atas
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            // Menyambungkan ke ID karya yang dibeli
            $table->foreignId('karya_id')->constrained('karyas')->onDelete('cascade');
            $table->decimal('harga_satuan', 12, 2);
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
    }
};
