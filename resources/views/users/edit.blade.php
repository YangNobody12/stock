<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ใช้งาน - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/user/styleue.css') }}">
</head>

<body>

    <!-- Sidebar ด้านซ้าย -->
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>
                Stock System<br>
                <span>ระบบจัดการสต็อก</span>
            </div>
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

    <!-- พื้นที่เนื้อหาหลัก -->
    <main class="main-content">
        <div class="container">
            
            <div class="header">
                <h1>✏️ แก้ไขข้อมูลผู้ใช้งาน</h1>
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

                <!-- ส่งข้อมูลไปยังฟังก์ชัน update พร้อมระบุ @method('PUT') ตามมาตรฐาน Resource Controller -->
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>ชื่อ - นามสกุล <span class="required">*</span></label>
                        <input
                            type="text"
                            name="name"
                            placeholder="ชื่อพนักงาน"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>อีเมลสำหรับเข้าสู่ระบบ <span class="required">*</span></label>
                        <input
                            type="email"
                            name="email"
                            placeholder="employee@company.com"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                    </div>

        <div class="form-group">
            <label>ระดับสิทธิ์ผู้ใช้งาน <span class="required">*</span></label>
            <select name="role" required>
                <option value="user" {{ old('role', $user->role) == 'User' ? 'selected' : '' }}>User (พนักงานทั่วไป)</option>
                <option value="it" {{ old('role', $user->role) == 'it' ? 'selected' : '' }}>IT (ฝ่ายไอที)</option>
                <option value="admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
            </select>
        </div>

                    <div class="row">
                        <div class="form-group">
                            <label>รหัสผ่านใหม่</label>
                            <input
                                type="password"
                                name="password"
                                placeholder="ปล่อยว่างไว้หากไม่ต้องการเปลี่ยน"
                            >
                            <div class="note">* กรอกเฉพาะกรณีต้องการเปลี่ยนรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)</div>
                        </div>
                        <div class="form-group">
                            <label>ยืนยันรหัสผ่านใหม่</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                            >
                        </div>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="btn-submit">💾 บันทึกการแก้ไข</button>
                        <a href="{{ route('users.index') }}" class="btn-cancel">ยกเลิก</a>
                    </div>

                </form>

            </div>

        </div>
    </main>

</body>
</html>