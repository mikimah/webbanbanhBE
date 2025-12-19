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
        Schema::create('SanPham', function (Blueprint $table) {
            $table->bigIncrements('MaSP');
            $table->string('TenSP', 30);
            $table->integer('GiaSP');
            $table->string('HinhSP', 200)->nullable();
            $table->foreignId('MaDM')
                    ->constrained('DanhMuc')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SanPham');
    }
};
