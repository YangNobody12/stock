<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ใช้งาน - ระบบจัดการสต็อก</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/user/styleuc.css') }}">
</head>
<body>

    <!-- Sidebar ด้านซ้าย -->
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>Stock System<br><span>ระบบจัดการสต็อก</span></div>
        </div>

               <div class="menu-label">เมนูหลัก</div>
            <!-- ฝ่าย IT จะไม่เห็นเมนูสต็อกเข้า-ออก  -->
            @if(auth()->check() && auth()->user()->role !== 'it')
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
            <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
                <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
                <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
            @endif
        </nav>

        @if(auth()->check() && auth()->user()->role === 'it')
        <div class="menu-label" style="margin-top: 15px;">การตั้งค่าระบบ</div>
        <nav class="menu">
                   <a href="{{ route('users.index') }}" class="active">👥 จัดการผู้ใช้งาน</a>
             <a href="{{ route('system.logs') }}">🔍 ตรวจสอบข้อมูลระบบ</a>
        </nav>
        @endif
        </nav>
        <div class="sidebar-bottom">
            <div class="user-profile">
                <div class="avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'Admin' }}</h4>
                    <p>{{ Auth::user()->email ?? 'admin@stock.com' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">🚪 ออกจากระบบ</button>
            </form>
        </div>
    </aside>

    <!-- พื้นที่เนื้อหาฟอร์ม -->
    <main class="main-content">
        <div class="container">
            <div class="header">
                <h1>👥 เพิ่มผู้ใช้งานใหม่</h1>
                <a href="{{ route('users.index') }}" class="btn-cancel">← ย้อนกลับ</a>
            </div>

            <div class="card">
                @if ($errors->any())
                    <div class="error-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>ชื่อ - นามสกุล <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="ชื่อพนักงาน" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label>อีเมลสำหรับเข้าสู่ระบบ <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="employee@company.com" value="{{ old('email') }}" required>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>รหัสผ่าน <span class="required">*</span></label>
                            <input type="password" name="password" placeholder="ตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร" required>
                        </div>
                        <div class="form-group">
                            <label>ยืนยันรหัสผ่าน <span class="required">*</span></label>
                            <input type="password" name="password_confirmation" placeholder="กรอกรหัสผ่านอีกครั้ง" required>
                        </div>
                    </div>
            <div class="form-group">
                <label>ระดับสิทธิ์ผู้ใช้งาน <span class="required">*</span></label>
                <select name="role" required>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (พนักงานทั่วไป)</option>
                    <option value="it" {{ old('role') == 'it' ? 'selected' : '' }}>IT (ฝ่ายไอที)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
                </select>
            </div>
                    <div class="buttons">
                        <button type="submit" class="btn-submit">💾 บันทึกผู้ใช้งาน</button>
                        <a href="{{ route('users.index') }}" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>