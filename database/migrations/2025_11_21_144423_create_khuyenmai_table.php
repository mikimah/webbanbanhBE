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
        Schema::create('KhuyenMai', function (Blueprint $table) {
            $table->bigIncrements('MaKM');
            $table->string('TenKM', 100);
            $table->string('LoaiKM', 20);
            $table->integer('GiaTri');
            $table->dateTime('NgayBD');
            $table->dateTime('NgayKT');
            $table->string('DieuKien', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KhuyenMai');
    }
};
