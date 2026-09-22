<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 🛑 ป้องกัน: อนุญาตให้เฉพาะฝ่าย IT เท่านั้นที่จัดการผู้ใช้งานได้
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าถึงหน้าจัดการผู้ใช้งาน (Users Index) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // ดึงข้อมูลผู้ใช้งานทั้งหมดมาแสดงในตาราง
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าหน้าเพิ่มผู้ใช้งาน (Users Create) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // แสดงหน้าฟอร์มเพิ่มผู้ใช้งาน
        return view('users.create');
    }

    public function store(Request $request)
    {
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนบันทึกเพิ่มผู้ใช้งาน (Users Store) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // บันทึก Log: เพิ่มผู้ใช้งานใหม่สำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_CREATED',
            'description' => 'เพิ่มผู้ใช้งานระบบ: ' . $user->name . ' (' . $user->email . ') สิทธิ์: ' . $user->role,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานระบบเรียบร้อยแล้ว');
    }

    // --- ฟังก์ชันสำหรับแสดงหน้าแก้ไขผู้ใช้งาน ---
    public function edit(Request $request, User $user)
    {
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนเข้าหน้าแก้ไขผู้ใช้งาน (Users Edit) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('users.edit', compact('user'));
    }

    // --- ฟังก์ชันสำหรับบันทึกข้อมูลที่แก้ไข ---
    public function update(Request $request, User $user)
    {
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนอัปเดตข้อมูลผู้ใช้งาน (Users Update) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // ถ้ามีการกรอกรหัสผ่านใหม่เข้ามา ถึงจะทำการเข้ารหัสและอัปเดต
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // บันทึก Log: แก้ไขข้อมูลผู้ใช้งานสำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_UPDATED',
            'description' => 'แก้ไขข้อมูลผู้ใช้งาน: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, User $user)
    {
        if (auth()->check() && strtolower(auth()->user()->role) !== 'it') {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'UNAUTHORIZED_ACCESS',
                'description' => 'พยายามฝ่าฝืนลบผู้ใช้งาน (Users Destroy) โดยไม่มีสิทธิ์',
                'ip_address' => $request->ip(),
            ]);

            abort(403, 'เฉพาะฝ่าย IT เท่านั้นที่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // ป้องกันไม่ให้ Admin/IT เผลอกดลบบัญชีของตัวเองที่กำลังล็อกอินอยู่
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'คุณไม่สามารถลบบัญชีของตัวเองได้']);
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        //  Log: ลบผู้ใช้งานสำเร็จ
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_DELETED',
            'description' => 'ลบบัญชีผู้ใช้งาน: ' . $userName . ' (' . $userEmail . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}