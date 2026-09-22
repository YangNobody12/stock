<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ActivityLog; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['product', 'supplier', 'user'])
            ->latest()
            ->get();

        return view('stock_ins.index', compact('stockIns'));
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('stock_ins.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // บันทึกรายการสินค้าเข้า
        $stockIn = StockIn::create([
            'product_id' => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'date' => $request->date,
            'note' => $request->note,
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->increment('quantity', $request->quantity);

        // 2. บันทึก Log ลงตาราง activity_logs อัตโนมัติ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'STOCK_IN',
            'description' => 'นำสินค้าเข้า: ' . $product->name . ' จำนวน ' . $request->quantity . ' ชิ้น',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('stock_ins.index')
            ->with('success', 'บันทึกสินค้าเข้าเรียบร้อยแล้ว');
    }
}