<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// หน้าแรก - auto redirect ไปยังหน้า login
Route::redirect('/', '/login');

// ระบบสมาชิก (Login / Logout)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔒 กลุ่มเส้นทางที่ต้องเข้าสู่ระบบก่อน
Route::middleware(['auth'])->group(function () {

    // แดชบอร์ด
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // จัดการโปรไฟล์ผู้ใช้
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 📦 ระบบจัดการสต็อกและข้อมูลหลัก
    Route::resource('products', ProductController::class);
    Route::resource('stock_ins', StockInController::class);
    Route::resource('stock_outs', StockOutController::class);

    // 🏷️ หมวดหมู่และผู้จำหน่าย (อนุญาตเฉพาะ Admin และ IT - เช็กสิทธิ์ใน Controller หรือใช้การคุมสิทธิ์ผ่านหน้าที่ View แทนเพื่อหลีกเลี่ยง Closure Error)
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);

    // 🛠️ โซนจัดการผู้ใช้งานและ System Logs (สำหรับฝ่าย IT เท่านั้น - ป้องกันโดยดักสิทธิ์ที่ Controller หรือใช้เงื่อนไขภายใน)
    Route::resource('users', UserController::class);
    Route::get('/system/logs', [SystemLogController::class, 'index'])->name('system.logs');

});
