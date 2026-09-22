<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ระบบจัดการสต็อก</title>

    <!-- นำเข้าฟอนต์ Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
            
            {{-- ฝ่าย IT จะไม่เห็นเมนูสินค้า/หมวดหมู่/สต็อก --}}
            @if(auth()->check() && auth()->user()->role !== 'it')
                <a href="{{ route('products.index') }}">📦 สินค้า</a>
                
                
                {{-- ผู้จำหน่าย: แสดงเฉพาะ Admin เท่านั้น (ซ่อนไม่ให้ User และ IT เห็น) --}}
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('suppliers.index') }}">🚚 ผู้จำหน่าย</a>
                    <a href="{{ route('categories.index') }}">🏷️ หมวดหมู่</a>
                @endif

                <a href="{{ route('stock_ins.index') }}">↓ สินค้าเข้า</a>
                <a href="{{ route('stock_outs.index') }}">↑ สินค้าออก</a>
            @endif
        </nav>

        {{-- โซนการตั้งค่าระบบ (แสดงเฉพาะฝ่าย IT เท่านั้น) --}}
        @if(auth()->check() && auth()->user()->role === 'it')
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
                <button type="submit" class="logout-btn">
                    🚪 ออกจากระบบ
                </button>
            </form>
        </div>
    </aside>

    <!-- พื้นที่เนื้อหาหลักด้านขวา -->
    <main class="main-content">
        
        <div class="header">
            <h1>แดชบอร์ด</h1>
            <p>ยินดีต้อนรับ {{ Auth::user()->name ?? 'Admin' }} · ข้อมูลอ้างอิง ณ เวลา: <span id="real-time-clock">กำลังโหลด...</span></p>
        </div>
        {{-- 1. ส่วนสำหรับ ADMIN (เห็นทุกอย่างครบถ้วน) --}}
    
        @if(auth()->check() && auth()->user()->role === 'admin')

            <!-- การ์ด 4 ใบด้านบน -->
            <div class="stat-cards">
                <div class="card">
                    <div class="card-icon icon-green">📦</div>
                    <div class="card-info">
                        <h3>สินค้าทั้งหมด</h3>
                        <div class="number">{{ \App\Models\Product::count() }}</div>
                        <div class="desc text-green">อัปเดตล่าสุดวันนี้</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon icon-blue">🏷️</div>
                    <div class="card-info">
                        <h3>หมวดหมู่</h3>
                        <div class="number">{{ \App\Models\Category::count() }}</div>
                        <div class="desc text-blue">จัดหมวดหมู่ครบถ้วน</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon icon-purple">🚚</div>
                    <div class="card-info">
                        <h3>ผู้จำหน่าย</h3>
                        <div class="number">{{ \App\Models\Supplier::count() }}</div>
                        <div class="desc text-purple">พาร์ทเนอร์ที่ใช้งาน</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon icon-orange">⚠️</div>
                    <div class="card-info">
                        <h3>สินค้าใกล้หมด</h3>
                        <div class="number">
                            {{ \App\Models\Product::whereColumn('quantity', '<=', 'minimum_stock')->count() }}
                        </div>
                        <div class="desc text-red">ต้องสั่งซื้อเพิ่ม</div>
                    </div>
                </div>
            </div>

            @php
                $topProducts = \App\Models\Product::orderByDesc('quantity')->take(5)->get();
                $barLabels = $topProducts->pluck('name')->toJson();
                $barData = $topProducts->pluck('quantity')->toJson();

                $categoryStats = \App\Models\Category::withCount('products')
                                    ->having('products_count', '>', 0)
                                    ->get();
                $donutLabels = $categoryStats->pluck('name')->toJson();
                $donutData = $categoryStats->pluck('products_count')->toJson();

                $months = [];
                $inData = [];
                $outData = [];
                
                for ($i = 5; $i >= 0; $i--) {
                    $date = \Carbon\Carbon::now()->subMonths($i);
                    $months[] = $date->translatedFormat('M Y'); 
                    
                    $inData[] = \App\Models\StockIn::whereMonth('date', $date->month)
                                               ->whereYear('date', $date->year)
                                               ->sum('quantity');
                    
                    $outData[] = \App\Models\StockOut::whereMonth('date', $date->month)
                                                ->whereYear('date', $date->year)
                                                ->sum('quantity');
                }
                $lineLabels = json_encode($months);
                $lineDataIn = json_encode($inData);
                $lineDataOut = json_encode($outData);
            @endphp

            <!-- โครงแถวที่ 1: กราฟ -->
            <div class="content-grid">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>สินค้าเข้า-ออก รายเดือน</h3>
                            <p>6 เดือนย้อนหลัง</p>
                        </div>
                    </div>
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>สัดส่วนตามหมวดหมู่</h3>
                            <p>จำนวนรายการสินค้า</p>
                        </div>
                    </div>
                    <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center; align-items: center;">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- โครงแถวที่ 2: กราฟแท่ง (Top 5) และ รายการสินค้าใกล้หมด -->
            <div class="content-grid">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>สินค้ายอดนิยม Top 5</h3>
                            <p>จำนวนคงเหลือในสต็อก</p>
                        </div>
                    </div>
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>⚠️ สินค้าใกล้หมด</h3>
                            <p>ต้องเติมสต็อก</p>
                        </div>
                    </div>
                    
                    <div class="stock-list">
                        @php
                            $lowStockProducts = \App\Models\Product::with('category')
                                                        ->whereColumn('quantity', '<=', 'minimum_stock')
                                                        ->orderBy('quantity', 'asc')
                                                        ->take(4)
                                                        ->get();
                        @endphp

                        @forelse ($lowStockProducts as $product)
                            @php
                                $statusClass = ($product->quantity <= 0) ? 'danger' : 'warning';
                            @endphp

                            <div class="stock-item {{ $statusClass }}">
                                <div class="stock-info">
                                    <h4>{{ $product->name }}</h4>
                                    <span>{{ $product->category->name ?? 'ไม่มีหมวดหมู่' }}</span>
                                </div>
                                <div class="stock-amount">
                                    <span class="current">{{ $product->quantity }}</span>
                                    <span class="max">/ {{ $product->minimum_stock }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #94a3b8; padding: 20px 0; font-size: 14px;">
                                ✅ ไม่มีสินค้าที่ใกล้หมดสต็อก
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- โครงแถวที่ 3: ตารางรายการล่าสุด -->
            <div class="panel panel-full" style="margin-bottom: 40px;">
                <div class="panel-header">
                    <div>
                        <h3>รายการล่าสุด</h3>
                    </div>
                </div>
                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>ประเภท</th>
                            <th>สินค้า</th>
                            <th>จำนวน</th>
                            <th>วันที่</th>
                            <th>ผู้ทำรายการ</th>
                        </tr>
                    </thead>

                    @php
                        $latestIns = \App\Models\StockIn::with(['product', 'user'])
                                            ->latest('date')->take(5)->get()
                                            ->map(function($item) {
                                                $item->transaction_type = 'in';
                                                return $item;
                                            });
                        
                        $latestOuts = \App\Models\StockOut::with(['product', 'user'])
                                            ->latest('date')->take(5)->get()
                                            ->map(function($item) {
                                                $item->transaction_type = 'out';
                                                return $item;
                                            });
                        
                        $recentTransactions = $latestIns->concat($latestOuts)
                                                ->sortByDesc('date')
                                                ->take(5);
                    @endphp

                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    @if ($transaction->transaction_type == 'in')
                                        <span class="badge-status badge-in">↓ เข้า</span>
                                    @else
                                        <span class="badge-status badge-out">↑ ออก</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->product->name ?? 'ลบสินค้าไปแล้ว' }}</td>
                                <td style="font-weight: 500;">
                                    {{ $transaction->quantity }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </td>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    📭 ยังไม่มีประวัติการทำรายการ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        {{-- 2. ส่วนสำหรับ USER (ซ่อนกราฟ, ซ่อน supplier, ซ่อนรายการล่าสุด) --}}
      
        @elseif(auth()->check() && auth()->user()->role === 'user')

            <!-- การ์ดสถิติเบื้องต้นเฉพาะสินค้า/หมวดหมู่/ใกล้หมด -->
            <div class="stat-cards">
                <div class="card">
                    <div class="card-icon icon-green">📦</div>
                    <div class="card-info">
                        <h3>สินค้าทั้งหมด</h3>
                        <div class="number">{{ \App\Models\Product::count() }}</div>
                        <div class="desc text-green">อัปเดตล่าสุดวันนี้</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon icon-blue">🏷️</div>
                    <div class="card-info">
                        <h3>หมวดหมู่</h3>
                        <div class="number">{{ \App\Models\Category::count() }}</div>
                        <div class="desc text-blue">จัดหมวดหมู่ครบถ้วน</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon icon-orange">⚠️</div>
                    <div class="card-info">
                        <h3>สินค้าใกล้หมด</h3>
                        <div class="number">
                            {{ \App\Models\Product::whereColumn('quantity', '<=', 'minimum_stock')->count() }}
                        </div>
                        <div class="desc text-red">ต้องสั่งซื้อเพิ่ม</div>
                    </div>
                </div>
            </div>

            <!-- แสดงเฉพาะรายการสินค้าใกล้หมดสำหรับ User (ไม่มีกราฟและไม่มีรายการล่าสุด) -->
            <div class="content-grid">
                <div class="panel" style="grid-column: span 2;">
                    <div class="panel-header">
                        <div>
                            <h3>⚠️ สินค้าใกล้หมดที่ต้องตรวจสอบ</h3>
                            <p>แจ้งเตือนสต็อกคงเหลือ</p>
                        </div>
                    </div>
                    
                    <div class="stock-list">
                        @php
                            $lowStockProducts = \App\Models\Product::with('category')
                                                        ->whereColumn('quantity', '<=', 'minimum_stock')
                                                        ->orderBy('quantity', 'asc')
                                                        ->take(6)
                                                        ->get();
                        @endphp

                        @forelse ($lowStockProducts as $product)
                            @php
                                $statusClass = ($product->quantity <= 0) ? 'danger' : 'warning';
                            @endphp

                            <div class="stock-item {{ $statusClass }}">
                                <div class="stock-info">
                                    <h4>{{ $product->name }}</h4>
                                    <span>{{ $product->category->name ?? 'ไม่มีหมวดหมู่' }}</span>
                                </div>
                                <div class="stock-amount">
                                    <span class="current">{{ $product->quantity }}</span>
                                    <span class="max">/ {{ $product->minimum_stock }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #94a3b8; padding: 30px 0; font-size: 14px;">
                                ✅ ไม่มีสินค้าที่ใกล้หมดสต็อกในขณะนี้
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        {{-- ========================================================= --}}
        {{-- 3. ส่วนสำหรับ IT (เห็นแค่ข้อความต้อนรับและเวลาปัจจุบันเท่านั้น) --}}
        {{-- ========================================================= --}}
        @elseif(auth()->check() && auth()->user()->role === 'it')

            <div class="panel" style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <div style="font-size: 48px; margin-bottom: 15px;">🛠️</div>
                <h3 style="font-size: 20px; color: #0f172a; margin-bottom: 10px;">ระบบจัดการและตรวจสอบระบบ (IT Support)</h3>
                <p style="color: #64748b; font-size: 14px;">คุณเข้าสู่ระบบในฐานะผู้ดูแลระบบไอที สามารถจัดการผู้ใช้งานและตรวจสอบ Logs ได้จากเมนูด้านซ้าย</p>
            </div>

        @endif

    </main>

    <!-- Script สำหรับอัปเดตเวลาจริงของเครื่อง -->
    <script>
        function updateTime() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false
            };
            document.getElementById('real-time-clock').textContent = now.toLocaleDateString('th-TH', options);
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    </script>
    
    <!-- นำเข้าไลบรารี Chart.js (โหลดเฉพาะตอนที่ Admin ล็อกอินใช้งาน) -->
    @if(auth()->check() && auth()->user()->role === 'admin')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // 1. กราฟเส้น (Line Chart)
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! $lineLabels !!},
                datasets: [
                    {
                        label: 'สินค้าเข้า',
                        data: {!! $lineDataIn !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'สินค้าออก',
                        data: {!! $lineDataOut !!},
                        borderColor: '#06b6d4',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', align: 'end' } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { dash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. กราฟโดนัท (Donut Chart)
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: {!! $donutLabels !!},
                datasets: [{
                    data: {!! $donutData !!},
                    backgroundColor: [
                        '#3b82f6', '#06b6d4', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. กราฟแท่ง (Bar Chart)
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! $barLabels !!},
                datasets: [{
                    label: 'จำนวนคงเหลือ',
                    data: {!! $barData !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { dash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
    @endif
</body>
</html>