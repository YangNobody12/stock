<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog; // นำเข้า Model สำหรับบันทึก Logs
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // แสดงรายการทั้งหมด (รองรับช่องค้นหา)
    public function index(Request $request)
    {
        // ป้องกันไม่ให้ User เข้าใช้งาน (ถ้าเป็น User ให้ดีดออกเป็นหน้า 403 ทันที)
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $search = $request->input('search');

        $categories = Category::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        })->get();

        return view('categories.index', compact('categories'));
    }

    // แสดงหน้าฟอร์มเพิ่มข้อมูล
    public function create()
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('categories.create');
    }

    // บันทึกข้อมูลใหม่ลงฐานข้อมูล
    public function store(Request $request)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // นทึก Log เมื่อเพิ่มหมวดหมู่
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CATEGORY_CREATED',
            'description' => 'เพิ่มหมวดหมู่สินค้า: ' . $category->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'เพิ่มหมวดหมู่เรียบร้อยแล้ว');
    }

    // แสดงหน้าฟอร์มแก้ไขข้อมูล
    public function edit(Category $category)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('categories.edit', compact('category'));
    }

    // อัปเดตข้อมูลที่แก้ไขลงฐานข้อมูล
    public function update(Request $request, Category $category)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // บันทึก Log เมื่อแก้ไขหมวดหมู่
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CATEGORY_UPDATED',
            'description' => 'แก้ไขหมวดหมู่สินค้า: ' . $category->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'แก้ไขหมวดหมู่สินค้าเรียบร้อยแล้ว');
    }

    // ลบข้อมูลออกจากฐานข้อมูล
    public function destroy(Category $category)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $categoryName = $category->name;
        $category->delete();

        //  บันทึก Log เมื่อลบหมวดหมู่
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CATEGORY_DELETED',
            'description' => 'ลบหมวดหมู่สินค้า: ' . $categoryName,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'ลบหมวดหมู่สินค้าเรียบร้อยแล้ว');
    }
}