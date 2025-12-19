<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'SanPham'; // Ten bang trong CSDL
    protected $primaryKey = 'MaSP';// Khoa chinh
    public $incrementing = true;// Khoa chinh tu tang
    protected $keyType = 'int';// Kieu du lieu khoa chinh
    public $timestamps = false; // Ko su dung created_at va updated_at

    protected $fillable = [
        'TenSP',
        'GiaSP',
        'HinhSP',
        'MaDM'
    ];

    public function danhMuc()
    {
        return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM');
    }
    public function chiTietDonHang()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaSP', 'MaSP');
    }
}
