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
            $table->string('TenKM', 100)->unique();
            $table->string('LoaiKM', 20);
            $table->integer('GiaTri');
            $table->dateTime('NgayBD');
            $table->dateTime('NgayKT');
            $table->integer('ToiThieu')->nullable();
            $table->integer('ToiDa')->nullable();
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
