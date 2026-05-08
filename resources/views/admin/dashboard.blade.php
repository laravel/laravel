<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin - Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }

    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0;
    }
    .sidebar-logo {
      padding: 1.5rem;
      color: white;
      font-size: 20px;
      font-weight: 700;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar-logo span { font-size: 13px; display: block; opacity: 0.6; font-weight: 400; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 1rem 0; }
    .nav-item a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 1.5rem;
      color: rgba(255,255,255,0.7);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s;
    }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-icon { font-size: 16px; width: 20px; text-align: center; }
    .sidebar-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .btn-logout {
      display: flex;
      align-items: center;
      gap: 10px;
      color: rgba(255,255,255,0.6);
      font-size: 14px;
      cursor: pointer;
      background: none;
      border: none;
      width: 100%;
      padding: 8px 0;
      font-family: inherit;
    }
    .btn-logout:hover { color: white; }

    .main { margin-left: 240px; flex: 1; }
    .topbar {
      background: white;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .topbar h1 { font-size: 18px; font-weight: 700; color: #1a3a52; }
    .topbar-right { font-size: 13px; color: #888; }

    .content { padding: 2rem; }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.25rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .stat-icon {
      width: 50px; height: 50px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
    }
    .stat-icon.blue { background: #ddeef8; }
    .stat-icon.green { background: #d4f5e9; }
    .stat-icon.orange { background: #fde8d0; }
    .stat-icon.purple { background: #e8d8f5; }
    .stat-icon.yellow { background: #fff3cd; }
    .stat-info h3 { font-size: 22px; font-weight: 700; color: #1a3a52; }
    .stat-info p { font-size: 12px; color: #888; margin-top: 2px; }

    .graphs-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
      margin-bottom: 2rem;
    }

    .section-title { font-size: 15px; font-weight: 700; color: #1a3a52; margin-bottom: 1rem; }
    .table-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      overflow: hidden;
    }
    .table-card.padded { padding: 1.5rem; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f7fafc; }
    th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; }
    td { padding: 12px 16px; font-size: 14px; color: #444; border-top: 1px solid #f0f0f0; }
    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
    }
    .badge.pending { background: #fff3cd; color: #856404; }
    .badge.confirmed { background: #d4f5e9; color: #0a6640; }
    .badge.completed { background: #ddeef8; color: #1a3a52; }
    .badge.cancelled { background: #fde8e8; color: #c0392b; }

    /* Pie chart container — fixed height so it doesn't overflow */
    .pie-wrapper {
      width: 100%;
      max-height: 260px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .pie-wrapper canvas {
      max-height: 260px !important;
    }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="sidebar-logo">
      🐟 eIsda
      <span>Admin Panel</span>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="active"><span class="nav-icon">📊</span> Dashboard</a></div>
      <div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
      <div class="nav-item"><a href="{{ route('admin.orders') }}"><span class="nav-icon">📋</span> Orders</a></div>
      <div class="nav-item"><a href="{{ route('admin.users') }}"><span class="nav-icon">👥</span> Users</a></div>
      <div class="nav-item"><a href="{{ route('admin.history') }}"><span class="nav-icon">🕒</span> History</a></div>
      <div class="nav-item"><a href="{{ route('admin.financial') }}"><span class="nav-icon">💰</span> Financial</a></div>
      <div class="nav-item"><a href="{{ route('admin.announcements') }}"><span class="nav-icon">📢</span> Announcements</a></div>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round" style="flex-shrink:0;">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Log Out
        </button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <h1>Dashboard</h1>
      <div class="topbar-right">Welcome, Admin</div>
    </div>
    <div class="content">

      <!-- STAT CARDS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">🐟</div>
          <div class="stat-info">
            <h3>{{ $totalProducts }}</h3>
            <p>Total Products</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">📋</div>
          <div class="stat-info">
            <h3>{{ $totalOrders }}</h3>
            <p>Total Reservations</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange">👥</div>
          <div class="stat-info">
            <h3>{{ $totalUsers }}</h3>
            <p>Total Users</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">💰</div>
          <div class="stat-info">
            <h3>₱{{ number_format($dailySales, 2) }}</h3>
            <p>Today's Sales</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✅</div>
          <div class="stat-info">
            <h3>{{ $dailyOrderCount }}</h3>
            <p>Orders Today</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow">⏳</div>
          <div class="stat-info">
            <h3>{{ $pendingCount }}</h3>
            <p>Pending Orders</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">✔️</div>
          <div class="stat-info">
            <h3>{{ $confirmedCount }}</h3>
            <p>Confirmed Orders</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">🕒</div>
          <div class="stat-info">
            <h3>{{ $totalTransactions }}</h3>
            <p>Completed Orders</p>
          </div>
        </div>
      </div>

      <!-- GRAPHS -->
      <div class="graphs-grid">
        <div class="table-card padded">
          <div class="section-title">📈 Monthly Sales (Last 6 Months)</div>
          <canvas id="salesChart" height="150"></canvas>
        </div>
        <div class="table-card padded">
          <div class="section-title">🥧 Order Status Breakdown</div>
          <div class="pie-wrapper">
            <canvas id="statusChart"></canvas>
          </div>
        </div>
      </div>
      <!-- END GRAPHS -->

      <!-- RECENT RESERVATIONS — FULL WIDTH -->
      <div class="section-title">Recent Reservations</div>
      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($recentReservations as $order)
            <tr>
              <td>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
              <td>{{ $order->user->name ?? 'N/A' }}</td>
              <td>{{ $order->items->map(fn($i) => $i->product->name ?? 'N/A')->join(', ') }}</td>
              <td>{{ $order->items->sum('quantity') }} kg</td>
              <td>{{ $order->created_at->format('M d, Y') }}</td>
              <td><span class="badge {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="6" style="text-align:center; color:#aaa; padding: 2rem;">No reservations yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script>
    let lastOrderId = {{ \App\Models\Order::latest()->value('id') ?? 0 }};
    let lastUpdatedAt = "{{ \App\Models\Order::latest('updated_at')->value('updated_at') ?? '' }}";

    function initCharts(salesLabels, salesData, statusData) {
      const salesCtx = document.getElementById('salesChart').getContext('2d');
      salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
          labels: salesLabels,
          datasets: [{
            label: 'Sales (₱)',
            data: salesData,
            backgroundColor: 'rgba(42, 125, 181, 0.7)',
            borderRadius: 8,
            borderSkipped: false,
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => '₱' + ctx.parsed.y.toLocaleString()
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { callback: val => '₱' + val.toLocaleString() }
            }
          }
        }
      });

      const statusCtx = document.getElementById('statusChart').getContext('2d');
      statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
          datasets: [{
            data: statusData,
            backgroundColor: ['#ffc107', '#28a745', '#2a7db5', '#dc3545'],
            borderWidth: 2,
            hoverOffset: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 15, font: { size: 12 } }
            },
            tooltip: {
              callbacks: {
                label: ctx => ctx.label + ': ' + ctx.parsed + ' orders'
              }
            }
          }
        }
      });
    }

    function updateDashboard(data) {
      const cards = document.querySelectorAll('.stat-info h3');
      cards[0].textContent = data.totalProducts;
      cards[1].textContent = data.totalOrders;
      cards[2].textContent = data.totalUsers;
      cards[3].textContent = '₱' + parseFloat(data.dailySales).toLocaleString('en-PH', { minimumFractionDigits: 2 });
      cards[4].textContent = data.dailyOrderCount;
      cards[5].textContent = data.pendingCount;
      cards[6].textContent = data.confirmedCount;
      cards[7].textContent = data.totalTransactions;

      salesChart.data.datasets[0].data = data.monthlySales.map(m => m.amount);
      salesChart.update();

      statusChart.data.datasets[0].data = [
        data.pendingCount,
        data.confirmedCount,
        data.completedCount,
        data.cancelledCount
      ];
      statusChart.update();

      let rows = '';
      data.recentReservations.forEach(order => {
        rows += `
          <tr>
            <td>#${String(order.id).padStart(4, '0')}</td>
            <td>${order.customer}</td>
            <td>${order.products}</td>
            <td>${order.quantity} kg</td>
            <td>${order.date}</td>
            <td><span class="badge ${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></td>
          </tr>`;
      });
      document.querySelector('tbody').innerHTML = rows ||
        '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:2rem;">No reservations yet.</td></tr>';
    }

    initCharts(
      {!! json_encode($monthlySales->pluck('label')) !!},
      {!! json_encode($monthlySales->pluck('amount')) !!},
      [{{ $pendingCount }}, {{ $confirmedCount }}, {{ $completedCount }}, {{ $cancelledCount }}]
    );

    setInterval(() => {
      fetch('{{ route("admin.dashboard.check") }}?last_id=' + lastOrderId + '&last_updated_at=' + encodeURIComponent(lastUpdatedAt))
        .then(res => res.json())
        .then(data => {
          if (data.hasNew) {
            lastOrderId = data.latestId;
            lastUpdatedAt = data.latestUpdatedAt;
            updateDashboard(data);
          }
        });
    }, 5000);
  </script>

</body>
</html>