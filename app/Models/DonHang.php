<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'DonHang'; // Ten bang trong CSDL
    protected $primaryKey = 'MaDH';// Khoa chinh
    public $incrementing = true;// Khoa chinh tu tang
    protected $keyType = 'int';// Kieu du lieu khoa chinh
    public $timestamps = false; // Ko su dung created_at va updated_at

    protected $fillable = [
        'SoLienHe',
        'DiaChiGiao',
        'NgayDat',
        'TrangThai',
        'MaND',
        'MaKM'
    ];
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND');
    }

    public function chiTietDonHang()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaDH', 'MaDH');
    }
}
