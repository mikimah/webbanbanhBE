<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('NguoiDung')->insert([
            'HoTen'=>'Admin',
            'Email'=>'admin@gmail.com',
            'MatKhau'=>Hash::make('123456'),
            'VaiTro'=>'admin',
        ]);
    }
}
