<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;

class DonHangController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'user'=>'nullable|integer|exists:NguoiDung,MaND',
            'phone'=>'required|string|max:15',
            'address'=>'required|string|max:255',

        ],[
            'user.exists' => 'Người dùng không tồn tại.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'address.required' => 'Địa chỉ là bắt buộc.',
        ]);

        $donHang = DonHang::create([
            'MaND' => $request->user,
            'SoLienHe' => $request->phone,
            'DiaChiGiao' => $request->address,
            'NgayDat' => now(),
            'TrangThai' => 'Đã thanh toán',
        ]);

        $cart = $request->cart;
        foreach ($cart as $item) {
            ChiTietDonHang::create([
                'MaDH' => $donHang->MaDH,
                'MaSP' => $item['id'],
                'SoLuong' => $item['qty'],
                'DonGia' => $item['price'],
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Đơn hàng đã được tạo thành công.',
            'order_id' => $donHang->MaDH,
        ]);

    }

    public function getAll()
    {
        $orders = DonHang::with('ChiTietDonHang.SanPham')->get();

        $orderSum = DonHang::count();
        $orderToday = DonHang::whereDate('NgayDat', now()->toDateString())->count();
        $orderMonth = DonHang::whereMonth('NgayDat', now()->month)->count();
        $orderYear = DonHang::whereYear('NgayDat', now()->year)->count();

        return response()->json([
            'status' => 200,
            'items' => $orders,
            'order_today' => $orderToday,
            'order_month'=> $orderMonth,
            'order_year'=> $orderYear,
            'order_sum'=> $orderSum,
        ]);
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
        ],[
            'startDate.required' => 'Ngày bắt đầu là bắt buộc.',
            'startDate.date_format' => 'Định dạng ngày bắt đầu không hợp lệ. Vui lòng sử dụng định dạng Y-m-d.',
            'endDate.required' => 'Ngày kết thúc là bắt buộc.',
            'endDate.date_format' => 'Định dạng ngày kết thúc không hợp lệ. Vui lòng sử dụng định dạng Y-m-d.',
            'endDate.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $orders = DonHang::with('ChiTietDonHang.SanPham')
            ->whereBetween('NgayDat', [$request->startDate, $request->endDate])
            ->get();

        $orderSum = DonHang::count();
        $orderToday = DonHang::whereDate('NgayDat', now()->toDateString())->count();
        $orderMonth = DonHang::whereMonth('NgayDat', now()->month)->count();
        $orderYear = DonHang::whereYear('NgayDat', now()->year)->count();
        return response()->json([
            'status' => 200,
            'items' => $orders,
            'order_today' => $orderToday,
            'order_month'=> $orderMonth,
            'order_year'=> $orderYear,
            'order_sum'=> $orderSum,
        ]);
    }
}
