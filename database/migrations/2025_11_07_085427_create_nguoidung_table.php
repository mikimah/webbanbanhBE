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
        Schema::create('NguoiDung', function (Blueprint $table) {
            $table->bigIncrements('MaND');
            $table->string('HoTen', 100);
            $table->string('Email', 100)->unique();
            $table->string('MatKhau', 255);
            $table->string('SoDienThoai', 15)->nullable();
            $table->string('DiaChi', 255)->nullable();
            $table->string('VaiTro', 10)->default('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('NguoiDung');
    }
};
