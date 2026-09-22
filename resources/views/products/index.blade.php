<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สินค้า - ระบบจัดการสต็อก</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/products/stylepi.css') }}">
</head>
<body>

    <!-- Sidebar ด้านซ้าย (จัดระเบียบเรียบร้อย ครบทุกเมนู) -->
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>Stock System<br><span>ระบบจัดการสต็อก</span></div>
        </div>
        <div class="menu-label">เมนูหลัก</div>
        <nav class="menu">
            <a href="{{ route('dashboard') }}">🏠 แดชบอร์ด</a>
            
            <a href="{{ route('products.index') }}">📦 สินค้า</a>
             @if(auth()->check() && strtolower(auth()->user()->role) !== 'user')
                <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
                <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
            @endif

            <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
            <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
        </nav>

        @if(auth()->check() && strtolower(auth()->user()->role) === 'it')
        <div class="menu-label" style="margin-top: 20px;">การตั้งค่าระบบ</div>
        <nav class="menu">
             <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
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

    <!-- พื้นที่เนื้อหาหลัก (ฝั่งขวาเลื่อนได้) -->
    <main class="main-content">
        <div class="header">
            <h1>📦 จัดการสินค้า</h1>
            <div class="header-actions">
                <form action="{{ route('products.index') }}" method="GET" class="search-box">
                    <input type="text" name="search" placeholder="ค้นหาสินค้าหรือรหัส..." value="{{ request('search') }}">
                    <button type="submit">🔍</button>
                </form>
                
                {{-- ซ่อนปุ่มเพิ่มสินค้าใหม่ ไม่ให้ User เห็น --}}
                @if(auth()->check() && strtolower(auth()->user()->role) !== 'user')
                    <a href="{{ route('products.create') }}" class="btn-add">+ เพิ่มสินค้าใหม่</a>
                @endif
            </div>
        </div>

        @php
            // ตรวจสอบว่ามีสินค้าใดบ้างที่มีจำนวนเหลือน้อยกว่าหรือเท่ากับค่าแจ้งเตือนขั้นต่ำ
            $lowStockCount = $products->filter(function($item) {
                return $item->quantity <= $item->minimum_stock;
            })->count();
        @endphp

        <!-- แจ้งเตือนสินค้าใกล้หมด -->
        @if($lowStockCount > 0)
            <div class="alert-warning">
                ⚠️ มีสินค้าใกล้หมดหรือถึงจุดต้องเติมสต็อกจำนวน <b>{{ $lowStockCount }}</b> รายการ โปรดตรวจสอบรายการด้านล่าง
            </div>
        @endif

        @if (session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>รหัส</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวดหมู่</th>
                        <th>ราคาขาย</th>
                        <th>คงเหลือ</th>
                        {{-- ซ่อนหัวข้อจัดการถ้าเป็น User --}}
                        @if(auth()->check() && strtolower(auth()->user()->role) !== 'user')
                            <th style="text-align: right;">จัดการ</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><b>{{ $product->product_code }}</b></td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ number_format($product->selling_price, 2) }} ฿</td>
                            <td>
                                <b>{{ $product->quantity }}</b>
                                @if($product->quantity <= $product->minimum_stock)
                                    <span class="badge-low-stock">ใกล้หมด</span>
                                @endif
                            </td>
                            
                            {{-- ซ่อนปุ่มแก้ไขและลบ ไม่ให้ User เห็น --}}
                            @if(auth()->check() && strtolower(auth()->user()->role) !== 'user')
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">✏️ แก้ไข</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบสินค้านี้?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">🗑️ ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->check() && strtolower(auth()->user()->role) !== 'user') ? 6 : 5 }}">
                                <div class="empty-state">
                                    <div>📭</div>ไม่พบข้อมูลสินค้าในระบบ
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>