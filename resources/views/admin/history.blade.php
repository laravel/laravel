<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin - History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }
    .sidebar { width: 240px; background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%); min-height: 100vh; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 50; }
    .sidebar-logo { padding: 1.5rem; color: white; font-size: 20px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
    .sidebar-logo span { font-size: 12px; display: block; opacity: 0.55; font-weight: 400; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 1rem 0; }
    .nav-item a { display: flex; align-items: center; gap: 12px; padding: 12px 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-item a i { width: 18px; text-align: center; font-size: 15px; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
    .btn-logout { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.6); font-size: 14px; cursor: pointer; background: none; border: none; width: 100%; padding: 8px 0; transition: color 0.2s; }
    .btn-logout:hover { color: white; }
    .main { margin-left: 240px; flex: 1; }
    .topbar { background: white; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .topbar h1 { font-size: 18px; font-weight: 800; color: #1a3a52; display: flex; align-items: center; gap: 10px; }
    .content { padding: 2rem; }

    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card { background: white; border-radius: 12px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .stat-icon.blue   { background: #ddeef8; color: #2a7db5; }
    .stat-icon.green  { background: #d4f5e9; color: #0a6640; }
    .stat-icon.red    { background: #fde8e8; color: #c0392b; }
    .stat-label { font-size: 11px; color: #6b7f8e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; }
    .stat-value { font-size: 22px; font-weight: 800; color: #1a3a52; }

    .filter-bar { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; }
    .filter-btn { padding: 7px 16px; border-radius: 20px; border: 1.5px solid #d0dce8; background: white; font-size: 12px; font-weight: 700; color: #6b7f8e; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
    .filter-btn:hover { border-color: #2a7db5; color: #2a7db5; }
    .filter-btn.active { background: #2a7db5; border-color: #2a7db5; color: white; }
    .filter-btn .count { background: rgba(255,255,255,0.3); padding: 1px 7px; border-radius: 10px; font-size: 11px; }
    .filter-btn:not(.active) .count { background: #f0f4f8; color: #6b7f8e; }

    .table-card { background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .table-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; }
    .table-header h2 { font-size: 14px; font-weight: 800; color: #1a3a52; }
    .search-box { display: flex; align-items: center; gap: 8px; background: #f0f4f8; border-radius: 8px; padding: 7px 12px; }
    .search-box input { border: none; background: none; outline: none; font-size: 13px; color: #1a3a52; width: 180px; }
    .search-box i { color: #6b7f8e; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f7fafc; }
    th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    td { padding: 13px 16px; font-size: 13px; color: #444; border-top: 1px solid #f0f4f8; vertical-align: middle; }
    tr:hover td { background: #fafcff; }
    .order-id { font-weight: 700; color: #1a3a52; }
    .customer-info strong { display: block; font-weight: 700; color: #1a3a52; font-size: 13px; }
    .customer-info span { font-size: 11px; color: #6b7f8e; }
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge.completed { background: #d4f5e9; color: #0a6640; }
    .badge.cancelled { background: #fde8e8; color: #c0392b; }
    .cancel-reason { font-size: 11px; color: #c0392b; font-style: italic; margin-top: 3px; max-width: 200px; }
    .empty-state { padding: 3rem; text-align: center; color: #6b7f8e; }
    .empty-state i { font-size: 3rem; opacity: 0.25; display: block; margin-bottom: 12px; }

    @media (max-width: 768px) { .main { margin-left: 0; } .sidebar { display: none; } .stats-row { grid-template-columns: repeat(2, 1fr); } }
  </style>
</head>
<body>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <i class="fa-solid fa-fish"></i>
      <div>eIsda <span>Admin Panel</span></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
<div class="nav-item"><a href="{{ route('admin.orders') }}"><span class="nav-icon">📋</span> Orders</a></div>
<div class="nav-item"><a href="{{ route('admin.users') }}"><span class="nav-icon">👥</span> Users</a></div>
<div class="nav-item"><a href="{{ route('admin.history') }}" class="active"><span class="nav-icon">🕒</span> History</a></div>
<div class="nav-item"><a href="{{ route('admin.financial') }}"><span class="nav-icon">💰</span> Financial</a></div>
<div class="nav-item"><a href="{{ route('admin.announcements') }}"><span class="nav-icon">📢</span> Announcements</a></div>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <h1><i class="fa-solid fa-clock-rotate-left" style="color:#2a7db5"></i> Order History</h1>
      <span style="font-size:12px;color:#6b7f8e;">{{ now()->format('F d, Y') }}</span>
    </div>
    <div class="content">

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-list"></i></div>
          <div><div class="stat-label">Total Records</div><div class="stat-value">{{ $orders->count() }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div><div class="stat-label">Completed</div><div class="stat-value">{{ $orders->where('status','completed')->count() }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-ban"></i></div>
          <div><div class="stat-label">Cancelled</div><div class="stat-value">{{ $orders->where('status','cancelled')->count() }}</div></div>
        </div>
      </div>

      <div class="filter-bar">
        <button class="filter-btn active" onclick="filterHistory('all', this)">
          <i class="fa-solid fa-list"></i> All <span class="count">{{ $orders->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterHistory('completed', this)">
          <i class="fa-solid fa-circle-check"></i> Completed <span class="count">{{ $orders->where('status','completed')->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterHistory('cancelled', this)">
          <i class="fa-solid fa-ban"></i> Cancelled <span class="count">{{ $orders->where('status','cancelled')->count() }}</span>
        </button>
      </div>

      <div class="table-card">
        <div class="table-header">
          <h2><i class="fa-solid fa-clock-rotate-left" style="color:#2a7db5;margin-right:6px"></i>Completed & Cancelled Orders</h2>
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search..." oninput="searchHistory(this.value)"/>
          </div>
        </div>
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Completed/Cancelled At</th>
                <th>Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody id="historyBody">
              @forelse($orders as $order)
              <tr data-status="{{ $order->status }}" data-search="{{ strtolower($order->customer_name . ' ' . $order->id) }}">
                <td><span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span><br><span style="font-size:11px;color:#6b7f8e;">{{ $order->created_at->format('M d, H:i') }}</span></td>
                <td>
                  <div class="customer-info">
                    <strong>{{ $order->customer_name }}</strong>
                    <span>{{ $order->customer_phone }}</span>
                    <span>{{ Str::limit($order->customer_address, 30) }}</span>
                  </div>
                </td>
                <td>
                  <div style="font-size:12px;color:#444">
                    @foreach($order->items as $item)
                    <span style="display:block">{{ $item->product->name ?? 'N/A' }} × {{ $item->quantity }}kg</span>
                    @endforeach
                  </div>
                </td>
                <td><strong style="color:#2a7db5">₱{{ number_format($order->total_amount, 2) }}</strong></td>
                <td style="font-size:12px;color:#444">{{ $order->updated_at->format('M d, Y H:i') }}</td>
                <td>
                  <span class="badge {{ $order->status }}">
                    @if($order->status === 'completed') <i class="fa-solid fa-circle-check"></i> Completed
                    @else <i class="fa-solid fa-ban"></i> Cancelled
                    @endif
                  </span>
                </td>
                <td>
                  @if($order->status === 'cancelled' && $order->cancel_reason)
                    <div class="cancel-reason"><i class="fa-solid fa-comment-dots"></i> {{ $order->cancel_reason }}</div>
                  @elseif($order->notes)
                    <span style="font-size:12px;color:#6b7f8e">{{ $order->notes }}</span>
                  @else
                    <span style="font-size:12px;color:#aab">—</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><p>Walang history pa.</p></div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    function filterHistory(status, btn) {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('#historyBody tr[data-status]').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
      });
    }
    function searchHistory(q) {
      const val = q.toLowerCase();
      document.querySelectorAll('#historyBody tr[data-status]').forEach(row => {
        row.style.display = row.dataset.search.includes(val) ? '' : 'none';
      });
    }
  </script>
</body>
</html>