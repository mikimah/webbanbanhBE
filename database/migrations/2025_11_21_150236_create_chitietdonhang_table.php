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
        Schema::create('ChiTietDonHang', function (Blueprint $table) {
            $table->bigIncrements('MaCTDH');
            $table->integer('SoLuong');
            $table->integer('DonGia');
            $table->foreignId('MaDH')
                    ->constrained('DonHang')
                    ->onDelete('cascade');
            $table->foreignId('MaSP')
                    ->constrained('SanPham')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ChiTietDonHang');
    }
};
