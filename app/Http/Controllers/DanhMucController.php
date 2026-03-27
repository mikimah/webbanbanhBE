<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class DanhMucController extends Controller
{
   public function add(Request $request) {
        $request->validate([
            'name' => 'required|string|max:30',
            'image' => 'required|string',
        ]);

            $category = DanhMuc::create([
                'TenDM' => $request->name,
                'HinhDM' => $request->image // Lưu link https://... vào DB
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Thêm danh mục thành công",
                'category' => $category
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

    // 1. Hàm cắt URL để lấy public_id
    private function getPublicIdFromUrl($url) {
        if (!$url) return null;
        // Lấy phần tên file sau dấu / cuối cùng và trước dấu chấm (extension)
        return pathinfo($url, PATHINFO_FILENAME);
    }

    // 2. Hàm gọi API xóa của Cloudinary
    public function deleteImageFromCloudinary($url) {
        $publicId = $this->getPublicIdFromUrl($url);
        if (!$publicId) return null;

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $timestamp = time();

        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $response = Http::withOptions([
            'verify' => false, // Fix lỗi cURL error 60 trên máy local của bạn
        ])->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ]);

        return $response->json();
    }

    // 3. Hàm delete danh mục đã cập nhật
    public function delete($id) {
        $item = DanhMuc::find($id);
        
        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy danh mục'
            ]);
        }

        $hasProduct = SanPham::where('MaDM', $id)->exists();
        if ($hasProduct) {
            return response()->json([
                'status' => 400,
                'message' => 'Không thể xoá vì còn sản phẩm thuộc danh mục này'
            ]);
        }

        // XỬ LÝ XOÁ ẢNH TRÊN CLOUDINARY
        if ($item->HinhDM) {
            try {
                $this->deleteImageFromCloudinary($item->HinhDM);
            } catch (\Exception $e) {
                // Log lỗi nếu cần nhưng vẫn tiếp tục xoá bản ghi trong DB
                Log::error("Cloudinary Delete Error: " . $e->getMessage());
            }
        }

        // Xoá danh mục trong Database
        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xoá danh mục và ảnh thành công'
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
