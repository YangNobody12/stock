<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog; // 1. นำเข้า ActivityLog Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // แสดงรายการสินค้าทั้งหมด (รองรับการค้นหา) - ทุกสิทธิ์เข้าดูได้
    public function index(Request $request)
    {
        $search = $request->input('search');
        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('product_code', 'like', "%{$search}%");
            })->get();

        return view('products.index', compact('products'));
    }

    // แสดงหน้าฟอร์มเพิ่มสินค้า
    public function create()
    {
        //  ป้องกันไม่ให้ User เข้าใช้งาน (ถ้าเป็น User ให้ดีดออกเป็นหน้า 403 ทันที)
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // บันทึกข้อมูลสินค้าใหม่
    public function store(Request $request)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'category_id' => 'required',
            'product_code' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($request->all());

        // บันทึก Log: เพิ่มสินค้าใหม่
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_CREATED',
            'description' => 'เพิ่มสินค้าใหม่: ' . $product->name . ' (รหัส: ' . $product->product_code . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('products.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }

    // แสดงหน้าฟอร์มแก้ไขข้อมูลสินค้า
    public function edit(Product $product)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // อัปเดตข้อมูลสินค้า
    public function update(Request $request, Product $product)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'category_id' => 'required',
            'product_code' => 'required|unique:products,product_code,' . $product->id,
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->all());

        // บันทึก Log: แก้ไขข้อมูลสินค้า
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_UPDATED',
            'description' => 'แก้ไขข้อมูลสินค้า: ' . $product->name . ' (รหัส: ' . $product->product_code . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('products.index')->with('success', 'แก้ไขข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    // ลบสินค้า
// ลบสินค้า
    public function destroy(Request $request, Product $product) // 👈 เพิ่ม Request $request ตรงนี้
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $productName = $product->name;
        $productCode = $product->product_code;
        $product->delete();

        // บันทึก Log: ลบสินค้า
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_DELETED',
            'description' => 'ลบสินค้า: ' . $productName . ' (รหัส: ' . $productCode . ')',
            'ip_address' => $request->ip(), // 👈 ตอนนี้ระบบจะรู้จัก $request แล้ว
        ]);

        return redirect()->route('products.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
}