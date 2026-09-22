<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลสินค้า - ระบบจัดการสต็อก</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/products/stylepe.css') }}">
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>Stock System<br><span>ระบบจัดการสต็อก</span></div>
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
             {{-- เพิ่มปุ่มตรวจสอบข้อมูลระบบสำหรับฝ่ายไอทีตามที่คุณต้องการ --}}
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

    <main class="main-content">
        <div class="container">
            <div class="header">
                <h1>✏️ แก้ไขข้อมูลสินค้า</h1>
                <a href="{{ route('products.index') }}" class="btn-cancel">← ย้อนกลับ</a>
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

                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="form-group">
                            <label>หมวดหมู่สินค้า <span class="required">*</span></label>
                            <select name="category_id" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>รหัสสินค้า <span class="required">*</span></label>
                            <input type="text" name="product_code" value="{{ old('product_code', $product->product_code) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ชื่อสินค้า <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>ราคาทุน (บาท)</label>
                            <input type="number" name="cost_price" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price) }}">
                        </div>

                        <div class="form-group">
                            <label>ราคาขาย (บาท) <span class="required">*</span></label>
                            <input type="number" name="selling_price" step="0.01" min="0" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>จำนวนแจ้งเตือนขั้นต่ำ (Minimum Stock)</label>
                            <input type="number" name="minimum_stock" min="0" value="{{ old('minimum_stock', $product->minimum_stock) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>รายละเอียดสินค้า</label>
                        <textarea name="description">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="btn-submit">💾 บันทึกการแก้ไข</button>
                        <a href="{{ route('products.index') }}" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>