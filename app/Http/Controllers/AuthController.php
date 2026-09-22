<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog; // 1. นำเข้า ActivityLog Model
use App\Models\User;        // 2. นำเข้า User Model เพื่อใช้ตรวจสอบกรณีล็อกอินพลาด

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // กรณีล็อกอินสำเร็จ
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // บันทึก Log: เข้าสู่ระบบสำเร็จ
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN',
                'description' => 'เข้าสู่ระบบสำเร็จ: ' . Auth::user()->email,
                'ip_address' => $request->ip(),
            ]);

            return redirect('/dashboard');
        }

        // บันทึก Log: กรณีรหัสผ่านผิด หรืออีเมลไม่ถูกต้อง
        $attemptedUser = User::where('email', $request->email)->first();
        ActivityLog::create([
            'user_id' => $attemptedUser ? $attemptedUser->id : null,
            'action' => 'LOGIN_FAILED',
            'description' => 'พยายามเข้าสู่ระบบแต่รหัสผ่านผิด/ไม่พบอีเมล: ' . $request->email,
            'ip_address' => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // บันทึก Log: ออกจากระบบ
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'LOGOUT',
                'description' => 'ออกจากระบบ: ' . $user->email,
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}