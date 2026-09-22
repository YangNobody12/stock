<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้จำหน่าย - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supplier/stylese.css') }}">

</head>
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
            
            {{-- Admin และ IT มองเห็นเมนูสินค้า หมวดหมู่ ได้ (User ก็มองเห็นแต่ถูกจำกัดการกดจัดการ) ส่วน Supplier ซ่อนไม่ให้ User เห็น --}}
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
             @if(auth()->check() && auth()->user()->role !== 'user')
                <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
                <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
            @endif

            {{-- ทุกสิทธิ์สามารถทำรายการ Stock In / Stock Out  --}}
            <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
            <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
        </nav>

        {{-- โซนการตั้งค่าระบบ (เฉพาะ IT และ Admin หรือตามสิทธิ์ที่กำหนด) --}}
        {{-- เนื่องจากตกลงกันว่า Admin ไม่ต้องมีหน้าจัดการผู้ใช้งาน เมนูนี้จึงให้แสดงเฉพาะฝ่าย IT เท่านั้น --}}
        @if(auth()->check() && auth()->user()->role === 'it')
        <div class="menu-label" style="margin-top: 20px;">การตั้งค่าระบบ</div>
        <nav class="menu">
             <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
             {{-- เพิ่มปุ่มตรวจสอบข้อมูลระบบสำหรับฝ่ายไอทีตาม--}}
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
    <!-- พื้นที่เนื้อหาหลัก -->
    <main class="main-content">
        <div class="container">
            
            <div class="header">
                <h1>🚚 แก้ไขข้อมูลผู้จำหน่าย</h1>
                <a href="{{ route('suppliers.index') }}" class="btn-cancel">← ย้อนกลับ</a>
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

                <!-- ส่งข้อมูลไปยังฟังก์ชัน update ของ SupplierController พร้อมระบุ @method('PUT') -->
                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>ชื่อบริษัท / ชื่อผู้จำหน่าย <span class="required">*</span></label>
                        <input
                            type="text"
                            name="name"
                            placeholder="ระบุชื่อผู้จำหน่าย"
                            value="{{ old('name', $supplier->name) }}"
                            required
                        >
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>ชื่อผู้ติดต่อ</label>
                            <input
                                type="text"
                                name="contact_person"
                                placeholder="ชื่อผู้ติดต่อ"
                                value="{{ old('contact_person', $supplier->contact_person) }}"
                            >
                        </div>

                        <div class="form-group">
                            <label>เบอร์โทรศัพท์</label>
                            <input
                                type="text"
                                name="phone"
                                placeholder="0812345678"
                                value="{{ old('phone', $supplier->phone) }}"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label>อีเมล</label>
                        <input
                            type="email"
                            name="email"
                            placeholder="supplier@email.com"
                            value="{{ old('email', $supplier->email) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label>ที่อยู่</label>
                        <textarea
                            name="address"
                            placeholder="ระบุที่อยู่ผู้จำหน่าย..."
                        >{{ old('address', $supplier->address) }}</textarea>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="btn-submit">💾 บันทึกการแก้ไข</button>
                        <a href="{{ route('suppliers.index') }}" class="btn-cancel">ยกเลิก</a>
                    </div>

                </form>

            </div>

        </div>
    </main>

</body>
</html>