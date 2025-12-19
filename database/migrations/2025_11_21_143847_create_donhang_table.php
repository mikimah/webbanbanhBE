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
        Schema::create('DonHang', function (Blueprint $table) {
            $table->bigIncrements('MaDH');
            $table->string('SoLienHe', 15);
            $table->string('DiaChiGiao', 255);
            $table->dateTime('NgayDat');
            $table->string('TrangThai', 20);
            $table->foreignId('MaND')->nullable()
                    ->constrained('NguoiDung')       // FK tới bảng NguoiDung
                    ->onDelete('cascade'); 
            $table->foreignId('MaKM')->nullable()
                    ->constrained('KhuyenMai')       // FK tới bảng KhuyenMai
                    ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DonHang');
    }
};
