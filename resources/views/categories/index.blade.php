<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หมวดหมู่สินค้า - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/categories/styleci.css') }}">
</head>

<body>

    <!-- Sidebar ด้านซ้าย (ล็อกติดหนึบ) -->
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
            
            {{-- Admin และ IT มองเห็นเมนูสินค้า หมวดหมู่ ได้ (User ก็มองเห็นแต่ถูกจำกัดการกดจัดการ) ส่วน Supplier ซ่อนไม่ให้ User เห็น --}}
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
            <a href="{{ route('dashboard') }}">🏠 แดชบอร์ด</a>
            <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
            
            @if(auth()->check() && auth()->user()->role !== 'user')
                <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
            @endif

            {{-- ทุกสิทธิ์สามารถทำรายการ Stock In / Stock Out --}}
            <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
            <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
        </nav>

        {{-- โซนการตั้งค่าระบบ (เฉพาะ IT และ Admin หรือตามสิทธิ์ที่กำหนด) --}}
        {{-- เนื่องจากตกลงกันว่า Admin ไม่ต้องมีหน้าจัดการผู้ใช้งาน เมนูนี้จึงให้แสดงเฉพาะฝ่าย IT เท่านั้น --}}
        @if(auth()->check() && auth()->user()->role === 'it')
        <div class="menu-label" style="margin-top: 20px;">การตั้งค่าระบบ</div>
        <nav class="menu">
             <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
             {{-- เพิ่มปุ่มตรวจสอบข้อมูลระบบสำหรับฝ่ายไอที --}}
             <a href="{{ route('system.logs') }}">🔍 ตรวจสอบข้อมูลระบบ</a>
        </nav>
        @endif

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

    <!-- พื้นที่เนื้อหาหลัก (เลื่อนดูเฉพาะฝั่งขวา) -->
    <main class="main-content">

        <div class="header">
            <h1>🏷️ หมวดหมู่สินค้า</h1>
            
            <div class="header-actions">
                <!-- ฟอร์มค้นหา -->
                <form action="{{ route('categories.index') }}" method="GET" class="search-box">
                    <input type="text" name="search" placeholder="ค้นหาหมวดหมู่..." value="{{ request('search') }}">
                    <button type="submit">🔍</button>
                </form>

                <a href="{{ route('categories.create') }}" class="btn-add">
                    + เพิ่มหมวดหมู่
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>รายละเอียด</th>
                            <th style="text-align: right;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="td-id">
                                    {{ $category->id }}
                                </td>
                                <td class="td-name">
                                    {{ $category->name }}
                                </td>
                                <td class="td-desc">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- ปุ่มแก้ไข -->
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn-edit">
                                            ✏️ แก้ไข
                                        </a>

                                        <!-- ปุ่มลบ -->
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบหมวดหมู่นี้?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                🗑️ ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div>📭</div>
                                        ไม่พบข้อมูลหมวดหมู่สินค้า
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>