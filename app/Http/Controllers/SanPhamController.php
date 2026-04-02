<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SanPhamController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'price' => 'required|integer|min:0',
            'cate' => 'required|integer|exists:DanhMuc,MaDM',
            'image' => 'required|string',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.string'   => 'Tên sản phẩm phải là chuỗi ký tự',
            'name.max'      => 'Tên sản phẩm tối đa 30 ký tự',

            'price.required' => 'Giá sản phẩm không được để trống',
            'price.integer'  => 'Giá sản phẩm phải là số nguyên',
            'price.min'      => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',

            'cate.required'  => 'Danh mục không được để trống',
            'cate.integer'   => 'Danh mục phải là số nguyên',
            'cate.exists'    => 'Danh mục được chọn không tồn tại',

            'image.required' => 'Hình ảnh không được để trống',
            'image.string'   => 'Hình ảnh phải là đường dẫn hoặc URL',
        ]);

        $sanPham = SanPham::create([
            'TenSP' => $request->name,
            'GiaSP' => $request->price,
            'MaDM' => $request->cate,
            'HinhSP' => $request->image,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Thêm sản phẩm thành công',
            'product' => $sanPham,
        ]);
    }

    public function getAll()
    {
        $items = SanPham::with('danhMuc')->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items = $items->map(function ($item) {
            $item->image_url = $this->buildImageUrl($item->HinhSP);
            $item->TenDM = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function getByCate($id)
    {
        $items = SanPham::with('danhMuc')->where('MaDM', $id)->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items = $items->map(function ($item) {
            $item->image_url = $this->buildImageUrl($item->HinhSP);
            $item->TenDM = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function getById($id)
    {
        $item = SanPham::with('danhMuc')->find($id);

        if (!$item) {
            return response()->json([
                'status' => 200,
                'item' => null
            ]);
        }

        $item->image_url = $this->buildImageUrl($item->HinhSP);
        $item->TenDM = $item->danhMuc ? $item->danhMuc->TenDM : null;

        return response()->json([
            'status' => 200,
            'item'  => $item
        ]);
    }

    public function getByName($name)
    {
        $items = SanPham::with('danhMuc')->where('TenSP', 'like', '%' . $name . '%')->get();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 200,
                'items'  => []
            ]);
        }

        $items = $items->map(function ($item) {
            $item->image_url = $this->buildImageUrl($item->HinhSP);
            $item->TenDM = $item->danhMuc ? $item->danhMuc->TenDM : null;
            return $item;
        });

        return response()->json([
            'status' => 200,
            'items'  => $items
        ]);
    }

    private function buildImageUrl($image)
    {
        if (!$image) {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return asset('storage/' . ltrim($image, '/'));
    }

    private function getPublicIdFromUrl($url)
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $segments = explode('/', trim($path, '/'));
        $uploadIndex = array_search('upload', $segments, true);

        if ($uploadIndex === false) {
            return pathinfo($url, PATHINFO_FILENAME);
        }

        $publicIdParts = array_slice($segments, $uploadIndex + 1);
        if (!empty($publicIdParts) && preg_match('/^v\d+$/', $publicIdParts[0])) {
            array_shift($publicIdParts);
        }

        if (empty($publicIdParts)) {
            return null;
        }

        $lastSegment = array_pop($publicIdParts);
        $publicIdParts[] = pathinfo($lastSegment, PATHINFO_FILENAME);

        return implode('/', $publicIdParts);
    }

    private function deleteImageFromCloudinary($url)
    {
        $publicId = $this->getPublicIdFromUrl($url);
        if (!$publicId) {
            return null;
        }

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $timestamp = time();

        if (!$cloudName || !$apiKey || !$apiSecret) {
            return null;
        }

        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $response = Http::withOptions([
            'verify' => false,
        ])->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ]);

        return $response->json();
    }

    public function delete($id)
    {
        $item = SanPham::find($id);
        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy sản phẩm'
            ]);
        }

        if ($item->HinhSP) {
            try {
                if (filter_var($item->HinhSP, FILTER_VALIDATE_URL)) {
                    $this->deleteImageFromCloudinary($item->HinhSP);
                } elseif (file_exists(public_path('storage/' . $item->HinhSP))) {
                    unlink(public_path('storage/' . $item->HinhSP));
                }
            } catch (\Exception $e) {
                Log::error('Delete product image error: ' . $e->getMessage());
            }
        }

        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xoá sản phẩm thành công'
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = SanPham::find($id);
        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:30',
            'image' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'cate' => 'nullable|integer|exists:DanhMuc,MaDM',
        ]);

        if ($request->filled('name')) {
            $item->TenSP = $request->name;
        }
        if ($request->filled('price')) {
            $item->GiaSP = $request->price;
        }
        if ($request->filled('cate')) {
            $item->MaDM = $request->cate;
        }

        if ($request->filled('image')) {
            try {
                if ($item->HinhSP && filter_var($item->HinhSP, FILTER_VALIDATE_URL)) {
                    $this->deleteImageFromCloudinary($item->HinhSP);
                } elseif ($item->HinhSP && file_exists(public_path('storage/' . $item->HinhSP))) {
                    unlink(public_path('storage/' . $item->HinhSP));
                }
            } catch (\Exception $e) {
                Log::error('Update product image error: ' . $e->getMessage());
            } finally {
                $item->HinhSP = $request->image;
            }
        }

        $item->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật sản phẩm thành công',
            'item' => $item,
        ]);
    }
}
