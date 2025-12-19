<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'NguoiDung'; // Ten bang trong CSDL
    protected $primaryKey = 'MaND';// Khoa chinh
    public $incrementing = true;// Khoa chinh tu tang
    protected $keyType = 'int';// Kieu du lieu khoa chinh
    public $timestamps = false; // Ko su dung created_at va updated_at

    protected $fillable = [
        'HoTen',
        'Email',
        'MatKhau',
        'SoDienThoai',
        'DiaChi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }

    //Cho laravel biet table nao dung de xac thuc mat khau
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    public function donHang()
    {
        return $this->hasMany(DonHang::class, 'MaND', 'MaND');
    }
    
}
