<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    public function add(Request $request){
         $request->validate([
        'name' => 'required|string|max:30',
        'image' => 'required|image|mimes:jpg,jpeg,png',
        ],[
            'name.required'=>'Tên không được để trống',
            'image.required'=>'Hình ảnh không được để trống'
        ]);

        $path = $request->file('image')->store('images', 'public');

        $product = DanhMuc::create([
            'TenDM' => $request->name,
            'HinhDM' => $path,   
        ]);

        return response()->json([
            'status'=>200,
            'message' => "Thêm danh mục thành công",
            'product' => $product,
            'image_url' => asset('storage/' . $path)  // gửi URL đầy đủ cho frontend
        ]);
    }

    public function getById($id){
        $item = DanhMuc::find($id);
        if(!$item){
            return response()->json([
            'status' => 200,
            'item'  => null
        ]);
        }
        $item->image_url = asset('storage/' . $item->HinhDM);

        return response()->json([
            'status'=>200,
            'item'=>$item
        ]);
    }

    public function getAll(){
        $items = DanhMuc::all();
        
        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items=$items->map(
        function ($item) {
            $item->image_url = asset('storage/' . $item->HinhDM);
            return $item;
        });

        return response()->json([
            'status'=>200,
            'items'=>$items
        ]);
    }

    public function getByName($name){
            $items = DanhMuc::where('TenDM','like','%'.$name.'%')->get();
            if ($items->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'items'  => []
                ]);
            }
    
            $items=$items->map(
            function ($item) {
                $item->image_url = asset('storage/' . $item->HinhDM);
                return $item;
            });
    
            return response()->json([
                'status'=>200,
                'items'=>$items
            ]);
    }

    public function delete($id){
        $item = DanhMuc::find($id);
        if(!$item){
            return response()->json([
            'status' => 400,
            'message' => 'Không tìm thấy danh mục'
        ]);
        }
        $hasProduct= SanPham::where('MaDM',$id)->exists();
        if($hasProduct){
            return response()->json([
                'status' => 400,
                'message' => 'Không thể xoá vì còn sản phẩm thuộc danh mục này'
            ]);
        }

        if ($item->HinhDM && file_exists(public_path('storage/' . $item->HinhDM))) {
        unlink(public_path('storage/' . $item->HinhDM));
        }
        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xoá danh mục thành công'
        ]);
    }

    public function update(Request $request,$id){
        $item = DanhMuc::find($id);
         if(!$item){
            return response()->json([
            'status' => 400,
            'message' => 'Không tìm thấy danh mục'
        ], 404);}

        $request->validate([
        'name' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if($request->filled('name')){
            $item->TenDM = $request->name;
        }

        if ($request->hasFile('image')) {

            // Xoá ảnh cũ
            if ($item->HinhDM && file_exists(public_path('storage/' . $item->HinhDM))) {
                unlink(public_path('storage/' . $item->HinhDM));
            }

            // Lưu ảnh mới
            $path = $request->file('image')->store('images', 'public');

            // Lưu path vào DB
            $item->HinhDM = $path;
        }

        $item->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật danh mục thành công',
        ]);


    }
}
