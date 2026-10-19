<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_details', function (Blueprint $table) {
            $table->dropForeign(['karya_id']);
            $table->foreignId('karya_id')->nullable()->change();
            $table->foreign('karya_id')->references('id')->on('karyas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_details', function (Blueprint $table) {
            $table->dropForeign(['karya_id']);
            $table->foreignId('karya_id')->nullable(false)->change();
            $table->foreign('karya_id')->references('id')->on('karyas')->cascadeOnDelete();
        });
    }
};
