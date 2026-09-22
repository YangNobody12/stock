<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ActivityLog; // นำเข้า ActivityLog Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    // แสดงรายการผู้จำหน่ายทั้งหมด (รองรับช่องค้นหา)
    public function index(Request $request)
    {
        // 🛑 ป้องกันไม่ให้ User เข้าใช้งาน พร้อมบันทึก Log การฝ่าฝืนสิทธิ์
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าถึงหน้าจัดการผู้จำหน่าย (Suppliers Index) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $search = $request->input('search');

        $suppliers = Supplier::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->get();

        return view('suppliers.index', compact('suppliers'));
    }

    // แสดงหน้าฟอร์มเพิ่มผู้จำหน่าย
    public function create()
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าหน้าเพิ่มผู้จำหน่าย (Suppliers Create) โดยไม่มีสิทธิ์',
                'ip_address' => request()->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('suppliers.create');
    }

    // บันทึกข้อมูลผู้จำหน่ายใหม่
    public function store(Request $request)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนบันทึกเพิ่มผู้จำหน่าย (Suppliers Store) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        // บันทึก Log: เพิ่มผู้จำหน่ายสำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_CREATED',
            'description' => 'เพิ่มผู้จำหน่ายใหม่: ' . $supplier->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'เพิ่มผู้จำหน่ายเรียบร้อยแล้ว');
    }

    // แสดงหน้าฟอร์มแก้ไขข้อมูลผู้จำหน่าย
    public function edit(Supplier $supplier)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าหน้าแก้ไขผู้จำหน่าย (Suppliers Edit) โดยไม่มีสิทธิ์',
                'ip_address' => request()->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    // อัปเดตข้อมูลผู้จำหน่าย
    public function update(Request $request, Supplier $supplier)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนอัปเดตข้อมูลผู้จำหน่าย (Suppliers Update) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        // บันทึก Log: แก้ไขข้อมูลผู้จำหน่ายสำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_UPDATED',
            'description' => 'แก้ไขข้อมูลผู้จำหน่าย: ' . $supplier->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'แก้ไขข้อมูลผู้จำหน่ายเรียบร้อยแล้ว');
    }

    // ลบผู้จำหน่าย
    public function destroy(Request $request, Supplier $supplier)
    {
        if (auth()->check() && strtolower(auth()->user()->role) === 'user') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนลบผู้จำหน่าย (Suppliers Destroy) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $supplierName = $supplier->name;
        $supplier->delete();

        // บันทึก Log: ลบผู้จำหน่ายสำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_DELETED',
            'description' => 'ลบผู้จำหน่าย: ' . $supplierName,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'ลบผู้จำหน่ายเรียบร้อยแล้ว');
    }
}