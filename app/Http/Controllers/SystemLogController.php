<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        // ปองกัน: อนุญาตให้เฉพาะฝ่าย IT เท่านั้นที่เข้าดูหน้า System Logs ได้
        // หากเป็น Admin หรือ User แอบพิมพ์ URL เข้ามา จะถูกบันทึกประวัติการฝ่าฝืนและดีดออกทันที
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าถึงหน้าตรวจสอบข้อมูลระบบ (System Logs) โดยไม่มีสิทธิ์ (Role ของผู้ใช้คือ: ' . auth()->user()->role . ')',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $search = $request->input('search');

        // ดึงข้อมูล Logs จากฐานข้อมูล พร้อมรองรับการค้นหาและแบ่งหน้า
        $logs = ActivityLog::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('description', 'like', "%{$search}%")
                             ->orWhere('action', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('system.logs', compact('logs'));
    }
}