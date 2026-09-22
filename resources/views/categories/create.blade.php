<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหมวดหมู่สินค้า - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/categories/stylecc.css') }}">
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
        <nav class="menu">
            <a href="{{ route('dashboard') }}">🏠 แดชบอร์ด</a>
            
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
            <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
            
            @if(auth()->check() && auth()->user()->role !== 'user')
                <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
            @endif

            <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
            <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
        </nav>

        @if(auth()->check() && auth()->user()->role === 'it')
            <div class="menu-label" style="margin-top: 20px;">การตั้งค่าระบบ</div>
            <nav class="menu">
                 <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
                 <a href="{{ route('system.logs') }}">🔍 ตรวจสอบข้อมูลระบบ</a>
            </nav>
        @endif

        <div class="sidebar-bottom">
            <div class="user-profile">
                <div class="avatar">A</div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'Admin' }}</h4>
                    <p>{{ Auth::user()->email ?? 'admin@stock.com' }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    🚪 ออกจากระบบ
                </button>
            </form>
        </div>
    </aside>

    <!-- พื้นที่เนื้อหาฟอร์ม -->
    <main class="main-content">
        <div class="container">
            
            <div class="header">
                <h1>🏷️ เพิ่มหมวดหมู่สินค้า</h1>
                <a href="{{ route('categories.index') }}" class="btn-cancel">
                    ← ย้อนกลับ
                </a>
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

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>ชื่อหมวดหมู่ <span class="required">*</span></label>
                        <input
                            type="text"
                            name="name"
                            placeholder="เช่น เครื่องดื่ม, ขนม, อุปกรณ์สำนักงาน"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea
                            name="description"
                            placeholder="ระบุรายละเอียดเพิ่มเติมเกี่ยวกับหมวดหมู่นี้..."
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="btn-submit">
                            💾 บันทึกหมวดหมู่
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn-cancel">
                            ยกเลิก
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </main>

</body>
</html>