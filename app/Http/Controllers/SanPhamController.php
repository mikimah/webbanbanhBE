<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function add(Request $request){
        $request->validate([
            'name'   => 'required|string|max:30',
            'price'   => 'required|numeric|min:0',
            'cate'    => 'required|integer|exists:DanhMuc,MaDM',
            'image' => 'required|image|mimes:jpg,jpeg,png',
        ],[
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.string'   => 'Tên sản phẩm phải là chuỗi ký tự',
            'name.max'      => 'Tên sản phẩm tối đa 30 ký tự',

            'price.required' => 'Giá sản phẩm không được để trống',
            'price.numeric'  => 'Giá sản phẩm phải là số',
            'price.min'      => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',

            'cate.required'  => 'Danh mục không được để trống',
            'cate.integer'   => 'Danh mục phải là số nguyên',
            'cate.exists'    => 'Danh mục được chọn không tồn tại',

            'image.required' => 'Hình ảnh không được để trống',
            'image.image'    => 'File tải lên phải là hình ảnh',
            'image.mimes'    => 'Hình ảnh chỉ chấp nhận định dạng jpg, jpeg, png',
        ]);

        $path = $request->file('image')->store('images', 'public');

        $sanPham = SanPham::create([
            'TenSP'   => $request->name,
            'GiaSP'   => $request->price,
            'MaDM'    => $request->cate,
            'HinhSP' => $path,
        ]);

        return response()->json([
            'status'  => 200,
            'message' => 'Thêm sản phẩm thành công',
            'poduct'    => $sanPham,
            'image_url' => asset('storage/' . $path)  // gửi URL đầy đủ cho frontend
        ]);
    }

    public function getAll(){
        $items = SanPham::all();

       
        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

       
        $items = $items->map(function ($item) {
            $item->image_url = asset('storage/' . $item->HinhSP);
            $item->TenDM     = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function getByCate($id){

        $items =  SanPham::where('MaDM', $id)->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items = $items->map(function ($item) {
            $item->image_url = asset('storage/' . $item->HinhSP);
            $item->TenDM     = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function getById($id){

        $item =  SanPham::where('MaSP', $id)->first();

        if (!$item) {
        return response()->json([
            'status' => 200,
            'item' =>  null
        ]);
        }

        $item->image_url = asset('storage/' . $item->HinhSP);
        $item->TenDM     = $item->danhMuc ? $item->danhMuc->TenDM : null;
       

        return response()->json([
            'status' => 200,
            'item'  => $item
        ]);
    }

    public function getByName($name){

        $items =  SanPham::where('TenSP', 'like', '%' . $name . '%')->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items = $items->map(function ($item) {
            $item->image_url = asset('storage/' . $item->HinhSP);
            $item->TenDM     = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function delete($id){
        $item = SanPham::find($id);
        if(!$item){
            return response()->json([
            'status' => 400,
            'message' => 'Không tìm thấy sản phẩm'
        ]);
        }
        if ($item->HinhSP && file_exists(public_path('storage/' . $item->HinhSP))) {
        unlink(public_path('storage/' . $item->HinhSP));
        }
        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xoá danh mục thành công'
        ]);
    }


        public function update(Request $request,$id){
        $item = SanPham::find($id);
         if(!$item){
            return response()->json([
            'status' => 400,
            'message' => 'Không tìm thấy danh mục'
        ], 404);}

        $request->validate([
        'name' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png',  
        'price'   => 'nullable|numeric|min:0',
        'cate'    => 'nullable|integer|exists:DanhMuc,MaDM',
        ]);

        if($request->filled('name')){
            $item->TenSP = $request->name;
        }
        if($request->filled('price')){
            $item->GiaSP = $request->price;
        }
         if($request->filled('cate')){
            $item->MaDM = $request->cate;
        }

        if ($request->hasFile('image')) {

            // Xoá ảnh cũ
            if ($item->HinhDM && file_exists(public_path('storage/' . $item->HinhSP))) {
                unlink(public_path('storage/' . $item->HinhSP));
            }

            // Lưu ảnh mới
            $path = $request->file('image')->store('images', 'public');

            // Lưu path vào DB
            $item->HinhSP = $path;
        }

        $item->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật danh mục thành công',
        ]);


    }
}
