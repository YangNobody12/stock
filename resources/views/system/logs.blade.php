<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบข้อมูลระบบ (System Logs) - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Prompt', sans-serif; background: #f4f7fb; color: #334155; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #111827; color: #94a3b8; display: flex; flex-direction: column; flex-shrink: 0; height: 100vh; overflow-y: auto; }
        .brand { padding: 25px 20px; display: flex; align-items: center; gap: 15px; color: white; font-weight: 600; font-size: 16px; }
        .brand .logo-icon { background: #3b82f6; width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .brand span { font-size: 12px; color: #94a3b8; font-weight: 400; }
        .menu-label { padding: 8px 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-top: 10px; color: #64748b; }
        .menu { display: flex; flex-direction: column; gap: 3px; padding: 0 12px; }
        .menu a { color: #94a3b8; text-decoration: none; padding: 10px 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-size: 14px; transition: 0.2s; }
        .menu a:hover { color: white; background: rgba(255, 255, 255, 0.05); }
        .menu a.active { background: #3b82f6; color: white; font-weight: 500; }
        
        .sidebar-bottom { margin-top: auto; padding: 15px 12px; border-top: 1px solid rgba(255, 255, 255, 0.05); }
        .user-profile { background: #1e293b; padding: 12px; border-radius: 10px; display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: white; }
        .avatar { width: 32px; height: 32px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; }
        .user-info h4 { font-size: 13px; }
        .user-info p { font-size: 11px; color: #94a3b8; }
        .logout-btn { width: 100%; background: transparent; border: none; color: #94a3b8; text-align: left; padding: 8px 12px; cursor: pointer; font-family: 'Prompt', sans-serif; font-size: 13px; border-radius: 6px; }
        .logout-btn:hover { color: white; background: rgba(255, 255, 255, 0.05); }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; height: 100vh; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header h1 { color: #0f172a; font-size: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        
        .card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); border: 1px solid #edf2f7; overflow: hidden; }
        .table-responsive { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { background: #f8fafc; color: #64748b; font-weight: 500; font-size: 13px; text-align: left; padding: 16px 25px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px 25px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        tr:hover td { background-color: #f8fafc; }
        
        .badge-action { background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; }
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state div { font-size: 30px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <!-- Sidebar ด้านซ้าย -->
    <aside class="sidebar">
        <div class="brand">
            <div class="logo-icon">📦</div>
            <div>Stock System<br><span>ระบบจัดการสต็อก</span></div>
        </div>

        <div class="menu-label">เมนูหลัก</div>
            <!-- ฝ่าย IT จะไม่เห็นเมนูสต็อกเข้า-ออก -->
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
             <a href="{{ route('users.index') }}">👥 จัดการผู้ใช้งาน</a>
             <a href="{{ route('system.logs') }}" class="active">🔍 ตรวจสอบข้อมูลระบบ</a>
        </nav>
        @endif

        <div class="sidebar-bottom">
            <div class="user-profile">
                <div class="avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'IT Support' }}</h4>
                    <p>{{ Auth::user()->email ?? 'it@stock.com' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">🚪 ออกจากระบบ</button>
            </form>
        </div>
    </aside>

    <!-- พื้นที่เนื้อหาหลัก (แสดง System Logs โดยเฉพาะ) -->
    <main class="main-content">
        <div class="header">
            <h1>🔍 ตรวจสอบข้อมูลการใช้งานระบบ (System Logs)</h1>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ผู้ใช้งาน (User)</th>
                            <th>การกระทำ (Action)</th>
                            <th>รายละเอียดระบบ</th>
                            <th>IP Address</th>
                            <th>วัน-เวลา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td style="font-weight: 600; color: #94a3b8;">{{ $log->id }}</td>
                                <td style="font-weight: 500; color: #0f172a;">{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge-action">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                                <td style="color: #64748b; font-size: 13px;">{{ $log->ip_address }}</td>
                                <td style="color: #64748b; font-size: 13px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div>📭</div>
                                        ยังไม่มีบันทึกข้อมูลประวัติการใช้งานระบบในขณะนี้
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