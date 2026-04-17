<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KhuyenMaiController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'TenKM' => 'required|string|max:100|unique:KhuyenMai,TenKM',
            'LoaiKM' => 'required|string|max:20',
            'GiaTri' => 'required|integer|min:0',
            'NgayBD' => 'required|date',
            'NgayKT' => 'required|date|after:NgayBD',
            'ToiThieu' => 'nullable|integer|min:0',
            'ToiDa' => 'nullable|integer|min:0|gte:ToiThieu',
        ], [
            'TenKM.required' => 'Tên khuyến mãi là bắt buộc',
            'TenKM.unique' => 'Tên khuyến mãi này đã tồn tại',
            'TenKM.max' => 'Tên khuyến mãi không được vượt quá 100 ký tự',
            'LoaiKM.required' => 'Loại khuyến mãi là bắt buộc',
            'GiaTri.required' => 'Giá trị khuyến mãi là bắt buộc',
            'NgayBD.required' => 'Ngày bắt đầu là bắt buộc',
            'NgayKT.required' => 'Ngày kết thúc là bắt buộc',
            'NgayKT.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'ToiDa.gte' => 'Giá trị tối đa phải lớn hơn hoặc bằng giá trị tối thiểu',
        ]);

        $khuyenMai = KhuyenMai::create($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Thêm khuyến mãi thành công',
            'data' => $khuyenMai,
        ]);
    }

    public function delete($MaKM)
    {
        $khuyenMai = KhuyenMai::find($MaKM);

        if (!$khuyenMai) {
            return response()->json([
                'status' => 404,
                'message' => 'Khuyến mãi không tồn tại',
            ], 404);
        }

        $khuyenMai->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xóa khuyến mãi thành công',
        ]);
    }

    public function update(Request $request, $MaKM)
    {
        $khuyenMai = KhuyenMai::find($MaKM);

        if (!$khuyenMai) {
            return response()->json([
                'status' => 404,
                'message' => 'Khuyến mãi không tồn tại',
            ], 404);
        }

        // Xác định NgayBD và NgayKT để kiểm tra
        $ngayBD = $request->filled('NgayBD') ? $request->NgayBD : $khuyenMai->NgayBD;
        $ngayKT = $request->filled('NgayKT') ? $request->NgayKT : $khuyenMai->NgayKT;

        $request->validate([
            'TenKM' => 'nullable|string|max:100|unique:KhuyenMai,TenKM,' . $MaKM . ',MaKM',
            'LoaiKM' => 'nullable|string|max:20',
            'GiaTri' => 'nullable|integer|min:0',
            'NgayBD' => 'nullable|date_format:Y-m-d H:i:s',
            'NgayKT' => 'nullable|date_format:Y-m-d H:i:s',
            'ToiThieu' => 'nullable|integer|min:0',
            'ToiDa' => 'nullable|integer|min:0',
        ], [
            'TenKM.unique' => 'Tên khuyến mãi này đã tồn tại',
            'TenKM.max' => 'Tên khuyến mãi không được vượt quá 100 ký tự',
            'TenKM.string' => 'Tên khuyến mãi phải là chuỗi ký tự',
            'LoaiKM.string' => 'Loại khuyến mãi phải là chuỗi ký tự',
            'LoaiKM.max' => 'Loại khuyến mãi không được vượt quá 20 ký tự',
            'GiaTri.integer' => 'Giá trị khuyến mãi phải là số nguyên',
            'GiaTri.min' => 'Giá trị khuyến mãi phải lớn hơn hoặc bằng 0',
            'NgayBD.date_format' => 'Định dạng ngày bắt đầu không hợp lệ Vui lòng sử dụng định dạng Y-m-d H:i:s',
            'NgayKT.date_format' => 'Định dạng ngày kết thúc không hợp lệ Vui lòng sử dụng định dạng Y-m-d H:i:s',
            'ToiThieu.integer' => 'Giá trị tối thiểu phải là số nguyên',
            'ToiThieu.min' => 'Giá trị tối thiểu phải lớn hơn hoặc bằng 0',
            'ToiDa.integer' => 'Giá trị tối đa phải là số nguyên',
            'ToiDa.min' => 'Giá trị tối đa phải lớn hơn hoặc bằng 0',
        ]);

        // Kiểm tra mối quan hệ NgayBD và NgayKT nếu có filled
        if ($request->filled('NgayBD') || $request->filled('NgayKT')) {
            if ($ngayKT <= $ngayBD) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Ngày kết thúc phải sau ngày bắt đầu',
                ], 422);
            }
        }

        if ($request->filled('TenKM')) {
            $khuyenMai->TenKM = $request->TenKM;
        }
        if ($request->filled('LoaiKM')) {
            $khuyenMai->LoaiKM = $request->LoaiKM;
        }
        if ($request->filled('GiaTri')) {
            $khuyenMai->GiaTri = $request->GiaTri;
        }
        if ($request->filled('NgayBD')) {
            $khuyenMai->NgayBD = $request->NgayBD;
        }
        if ($request->filled('NgayKT')) {
            $khuyenMai->NgayKT = $request->NgayKT;
        }
        if ($request->filled('ToiThieu')) {
            $khuyenMai->ToiThieu = $request->ToiThieu;
        }
        if ($request->filled('ToiDa')) {
            $khuyenMai->ToiDa = $request->ToiDa;
        }

        $khuyenMai->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật khuyến mãi thành công',
            'data' => $khuyenMai,
        ]);
    }

    public function getAll()
    {
        $khuyenMai = KhuyenMai::all();

        return response()->json([
            'status' => 200,
            'items' => $khuyenMai,
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'TenKM' => 'required|string',
            'MaND' => 'required|integer',
            'TongTotal' => 'required|integer|min:0'
        ], [
            'TenKM.required' => 'Tên khuyến mãi là bắt buộc',
            'TenKM.string' => 'Tên khuyến mãi phải là chuỗi ký tự',
            'MaND.required' => 'Mã người dùng là bắt buộc',
            'MaND.integer' => 'Mã người dùng phải là số nguyên',
            'TongTotal.required' => 'Tổng giá trị là bắt buộc',
            'TongTotal.integer' => 'Tổng giá trị phải là số nguyên',
            'TongTotal.min' => 'Tổng giá trị phải lớn hơn hoặc bằng 0',
        ]);

        // Kiểm tra xem tên khuyến mãi có tồn tại không
        $khuyenMai = KhuyenMai::where('TenKM', $request->TenKM)->first();

        if (!$khuyenMai) {
            return response()->json([
                'status' => 404,
                'message' => 'Khuyến mãi không tồn tại',
            ], 404);
        }

        // Kiểm tra xem còn hạn không
        $now = Carbon::now()->format('d/m/Y');
        $ngayBD = Carbon::parse($khuyenMai->NgayBD)->format('d/m/Y');
        $ngayKT = Carbon::parse($khuyenMai->NgayKT)->format('d/m/Y');

        if ( $now > $ngayKT) {
            return response()->json([
                'status' => 400,
                'message' => 'Khuyến mãi đã hết hạn',
            ], 400);
        }

        $temp = KhuyenMai::where('TenKM', $request->TenKM)->first();

        // Kiểm tra xem người dùng đã dùng khuyến mãi này chưa
        $donHangDaSuDung = DonHang::where('MaND', $request->MaND)
            ->where('MaKM', $temp->MaKM)
            ->exists();

        if ($donHangDaSuDung) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn đã sử dụng khuyến mãi này rồi',
            ], 400);
        }

        $tongTotal = $request->TongTotal;
        $giaTriKhuyenMai = $khuyenMai->GiaTri;
        $kieuKhuyenMai = $khuyenMai->LoaiKM;
        $minTotal = $khuyenMai->ToiThieu;
        $maxTotal = $khuyenMai->ToiDa == 0 ? PHP_INT_MAX : $khuyenMai->ToiDa;

        if ($tongTotal < $minTotal ) {
            return response()->json([
                'status' => 400,
                'message' => 'Tổng giá trị không hợp lệ',
            ], 400);
        }

        if ($kieuKhuyenMai === 'percent') {
            $giaTriGiam = $tongTotal * ($giaTriKhuyenMai / 100);
        } else {
            $giaTriGiam = $giaTriKhuyenMai;
        }

        if($giaTriGiam >= $maxTotal) {
            $giaTriGiam = $maxTotal;
        }

        $tongTotalSauGiam = $tongTotal - $giaTriGiam;

        return response()->json([
            'status' => 200,
            'message' => 'Khuyến mãi hợp lệ',
            'item' => $khuyenMai,
            'tongTotalSauGiam' => $tongTotalSauGiam,
        ]);
    }
}
