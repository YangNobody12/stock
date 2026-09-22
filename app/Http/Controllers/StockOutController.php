<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Product;
use App\Models\ActivityLog; // 1. นำเข้า ActivityLog Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOuts = StockOut::with(['product', 'user'])
            ->latest()
            ->get();

        return view('stock_outs.index', compact('stockOuts'));
    }

    public function create()
    {
        $products = Product::all();

        return view('stock_outs.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->quantity < $request->quantity) {
            return back()
                ->withErrors(['quantity' => 'จำนวนสินค้าในสต็อกไม่เพียงพอ'])
                ->withInput();
        }

        // บันทึกรายการสินค้าออก
        StockOut::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'date' => $request->date,
            'note' => $request->note,
        ]);

        $product->decrement('quantity', $request->quantity);

        // 2. แพิ่มโค้ดบันทึก Log ลงตาราง activity_logs อัตโนมัติ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'STOCK_OUT',
            'description' => 'เบิกสินค้าออก: ' . $product->name . ' จำนวน ' . $request->quantity . ' ชิ้น',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('stock_outs.index')
            ->with('success', 'บันทึกสินค้าออกเรียบร้อยแล้ว');
    }
}