<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้งาน - ระบบจัดการสต็อก</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/user/styleui.css') }}">
</head>
<body>

    <!-- Sidebar ด้านซ้าย -->
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>Stock System<br><span>ระบบจัดการสต็อก</span></div>
        </div>

        <div class="menu-label">เมนูหลัก</div>
            <!-- ฝ่าย IT จะไม่เห็นเมนูสต็อกเข้า-ออก-->
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
        <div class="header">
            <h1>👥 จัดการผู้ใช้งานระบบ</h1>
            <a href="{{ route('users.create') }}" class="btn-add">+ เพิ่มผู้ใช้งาน</a>
        </div>

        @if (session('success'))
            <div class="alert-success">✅ {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error">⚠️ {{ $errors->first() }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อีเมล</th>
                            <th>ระดับผู้ใช้ (Role)</th>
                            <th>วันที่สร้าง</th>
                            <th style="text-align: right;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="td-name">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-admin">Admin</span>
                                @elseif($user->role === 'it')
                                    <span class="badge badge-IT">IT</span>
                                @else
                                    <span class="badge badge-user">User</span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                             <td>
                            <div class="action-buttons" style="justify-content: flex-end; align-items: center;">
                                
                                <!--  ปุ่มแก้ไข: แสดงให้เห็นและกดได้เสมอ ทุกบัญชี (รวมถึงบัญชีของตัวเองด้วย) -->
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-edit">✏️ แก้ไข</a>

                                <!-- 🗑️ ปุ่มลบ: จะแสดงก็ต่อเมื่อไม่ใช่บัญชีของตัวเอง -->
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?');" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑️ ลบ</button>
                                    </form>
                                @else
                                    <span style="color: #94a3b8; font-size: 13px; margin-left: 5px;">(บัญชีของคุณ)</span>
                                @endif

                            </div>
                        </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div>👥</div>
                                        ยังไม่มีข้อมูลผู้ใช้งาน
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