<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าเข้า - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/stock_inout/styleoc.css') }}">
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
            
            {{-- Admin และ IT มองเห็นเมนูสินค้า หมวดหมู่ ได้ (User ก็มองเห็นแต่ถูกจำกัดการกดจัดการ) ส่วน Supplier ซ่อนไม่ให้ User เห็น --}}
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
            
             @if(auth()->check() && auth()->user()->role !== 'user')
                <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
                <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
            @endif

            {{-- ทุกสิทธิ์สามารถทำรายการ Stock In / Stock Out--}}
            <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
            <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
        </nav>

        {{-- โซนการตั้งค่าระบบ (เฉพาะ IT และ Admin หรือตามสิทธิ์ที่กำหนด) --}}
        {{-- เนื่องจากตกลงกันว่า Admin ไม่ต้องมีหน้าจัดการผู้ใช้งาน เมนูนี้จึงให้แสดงเฉพาะฝ่าย IT เท่านั้น --}}
        @if(auth()->check() && auth()->user()->role === 'it')
        <div class="menu-label" style="margin-top: 20px;">การตั้งค่าระบบ</div>
        <nav class="menu">
             <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
             {{-- เพิ่มปุ่มตรวจสอบข้อมูลระบบสำหรับฝ่ายไอที--}}
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
                <h1>📥 เพิ่มสินค้าเข้า</h1>
                <a href="{{ route('stock_ins.index') }}" class="btn-cancel">
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

                <form action="{{ route('stock_ins.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>เลือกสินค้า <span class="required">*</span></label>
                        <select name="product_id" required>
                            <option value="">-- เลือกสินค้าที่ต้องการเพิ่ม --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (คงเหลือปัจจุบัน: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ผู้จำหน่าย (รับมาจาก)</label>
                        <select name="supplier_id">
                            <option value="">-- เลือกผู้จำหน่าย --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>จำนวนที่รับเข้า <span class="required">*</span></label>
                            <input
                                type="number"
                                name="quantity"
                                min="1"
                                placeholder="เช่น 20"
                                value="{{ old('quantity') }}"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>วันที่รับเข้า <span class="required">*</span></label>
                            <input
                                type="date"
                                name="date"
                                value="{{ old('date', date('Y-m-d')) }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label>หมายเหตุ</label>
                        <textarea
                            name="note"
                            placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)..."
                        >{{ old('note') }}</textarea>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="btn-submit">
                            💾 บันทึกสินค้าเข้า
                        </button>
                        <a href="{{ route('stock_ins.index') }}" class="btn-cancel">
                            ยกเลิก
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </main>

</body>
</html>