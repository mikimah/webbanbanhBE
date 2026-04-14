<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKM';
    public $incrementing = true;// Khoa chinh tu tang
    protected $keyType = 'int';// Kieu du lieu khoa chinh
    public $timestamps = false;

    protected $fillable = [
        'TenKM',
        'LoaiKM',
        'GiaTri',
        'NgayBD',
        'NgayKT',
        'ToiThieu',
        'ToiDa',
    ];

    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaKM', 'MaKM');
    }
}
