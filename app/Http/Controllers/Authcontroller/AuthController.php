<?php

namespace App\Http\Controllers\Authcontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function user_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:NguoiDung,Email',
            'password' => 'required|string|min:6|confirmed',
        ],[
            'name.required' => 'Tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);
        $user = User::create([
            'HoTen' => $request->name,
            'Email' => $request->email,
            'MatKhau' => Hash::make($request->password),
        ]);
        return response()->json([
            'status'=>200,
            'message'=>'Đăng ký thành công',
        ]);
    }
    public function user_login(Request $request){
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ],[
            'email.required' => 'Email không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
        ]);

        $user = User::where('Email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng'],
            ]);
        }

    
        if (!Hash::check($request->password, $user->MatKhau)) {
            throw ValidationException::withMessages([
                'password' => ['Email hoặc mật khẩu không đúng'],
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status'=>200,
            'message'=>'Đăng nhập thành công',
            'access_token' => $token,
            'user' => $user,
            
        ]);
    }
    public function user_logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Đăng xuất thành công',
        ]);
    }
}
