<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'DanhMuc'; // Ten bang trong CSDL
    protected $primaryKey = 'MaDM';// Khoa chinh
    public $incrementing = true;// Khoa chinh tu tang
    protected $keyType = 'int';// Kieu du lieu khoa chinh
    public $timestamps = false; // Ko su dung created_at va updated_at

    protected $fillable = [
        'TenDM',
        'HinhDM'
    ];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MaDM', 'MaDM');
    }
}
