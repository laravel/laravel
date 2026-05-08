<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin – Financial Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }

    .sidebar { width: 240px; background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%); min-height: 100vh; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 50; }
    .sidebar-logo { padding: 1.5rem; color: white; font-size: 20px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
    .sidebar-logo small { font-size: 12px; display: block; opacity: 0.55; font-weight: 400; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 1rem 0; }
    .nav-item a { display: flex; align-items: center; gap: 12px; padding: 12px 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-icon { font-size: 16px; width: 20px; text-align: center; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
    .btn-logout { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.6); font-size: 14px; cursor: pointer; background: none; border: none; width: 100%; padding: 8px 0; font-family: inherit; }
    .btn-logout:hover { color: white; }

    .main { margin-left: 240px; flex: 1; min-height: 100vh; }
    .topbar { background: white; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,0.08); flex-wrap: wrap; }
    .topbar h1 { font-size: 18px; font-weight: 800; color: #1a3a52; display: flex; align-items: center; gap: 10px; }
    .topbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .date-form { display: flex; align-items: center; gap: 8px; }
    .date-form input[type="date"] { padding: 8px 12px; border: 1.5px solid #d0dce8; border-radius: 9px; font-size: 13px; font-family: inherit; color: #1a3a52; outline: none; cursor: pointer; }
    .btn-go { background: #2a7db5; color: white; border: none; border-radius: 9px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: inherit; }
    .btn-go:hover { background: #1f6090; }
    .btn-today { background: #f0f4f8; color: #1a3a52; border: 1.5px solid #d0dce8; border-radius: 9px; padding: 8px 14px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; }
    .btn-today:hover { background: #dde5ee; }
    .date-badge { background: #eef6fc; color: #2a7db5; font-size: 13px; font-weight: 700; padding: 7px 14px; border-radius: 9px; border: 1.5px solid #c5e0f5; display: flex; align-items: center; gap: 6px; }

    .content { padding: 2rem; }
    .section-title { font-size: 15px; font-weight: 800; color: #1a3a52; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }

    .table-card { background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 2rem; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f7fafc; }
    th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap; }
    td { padding: 13px 16px; font-size: 14px; color: #444; border-top: 1px solid #f0f0f0; vertical-align: middle; }
    tr:hover td { background: #fafcff; }
    .total-row td { background: #f0f7ff; font-weight: 800; color: #1a3a52; border-top: 2px solid #c5e0f5; }
    .profit-cell { color: #0a6640; font-weight: 700; }
    .loss-cell { color: #c0392b; font-weight: 700; }

    .status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-pill.pending   { background: #fff3cd; color: #856404; }
    .status-pill.confirmed { background: #d4f5e9; color: #0a6640; }
    .status-pill.completed { background: #ddeef8; color: #1a3a52; }
    .status-pill.cancelled { background: #fde8e8; color: #c0392b; }

    .empty-state { text-align: center; padding: 4rem 2rem; color: #6b7f8e; }
    .empty-state i { font-size: 3.5rem; opacity: 0.2; display: block; margin-bottom: 1rem; }
    .empty-state p { font-size: 14px; }
    .empty-state small { font-size: 12px; color: #aab; margin-top: 4px; display: block; }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    🐟
    <div>eIsda <small>Admin Panel</small></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
    <div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
    <div class="nav-item"><a href="{{ route('admin.orders') }}"><span class="nav-icon">📋</span> Orders</a></div>
    <div class="nav-item"><a href="{{ route('admin.users') }}"><span class="nav-icon">👥</span> Users</a></div>
    <div class="nav-item"><a href="{{ route('admin.history') }}"><span class="nav-icon">🕒</span> History</a></div>
    <div class="nav-item"><a href="{{ route('admin.financial') }}" class="active"><span class="nav-icon">💰</span> Financial</a></div>
    <div class="nav-item"><a href="{{ route('admin.announcements') }}"><span class="nav-icon">📢</span> Announcements</a></div>
  </nav>
  <div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Log Out
      </button>
    </form>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <h1><i class="fa-solid fa-peso-sign" style="color:#2a7db5"></i> Financial Report</h1>
    <div class="topbar-right">
      <div class="date-badge">
        <i class="fa-solid fa-calendar-day"></i>
        {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
      </div>
      <form method="GET" action="{{ route('admin.financial') }}" class="date-form">
        <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"/>
        <button type="submit" class="btn-go">
          <i class="fa-solid fa-magnifying-glass"></i> View
        </button>
      </form>
      @if($date !== now()->toDateString())
        <a href="{{ route('admin.financial') }}" class="btn-today">
          <i class="fa-solid fa-rotate-left"></i> Today
        </a>
      @endif
    </div>
  </div>

  <div class="content">

    <div class="section-title">
      <i class="fa-solid fa-table-list" style="color:#2a7db5"></i>
      Sales Breakdown — {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
    </div>

    <div class="table-card" id="mainTable">
      @if(empty($productStats))
        <div class="empty-state">
          <i class="fa-solid fa-fish"></i>
          <p>Walang orders sa araw na ito.</p>
          <small>Pumili ng ibang petsa o maghintay ng bagong orders.</small>
        </div>
      @else
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th><i class="fa-solid fa-fish"></i> Product</th>
              <th>Cost / kg</th>
              <th>Qty Ordered</th>
              <th>Gross Revenue</th>
              <th>Capital Used</th>
              <th>Net Profit</th>
            </tr>
          </thead>
          <tbody>
            @foreach($productStats as $stat)
            <tr>
              <td style="color:#6b7f8e;font-size:12px">{{ $loop->iteration }}</td>
              <td><strong style="color:#1a3a52"><i class="fa-solid fa-fish" style="color:#2a7db5;margin-right:6px;font-size:12px"></i>{{ $stat['name'] }}</strong></td>
              <td>₱{{ number_format($stat['cost_per_kg'], 2) }}</td>
              <td>{{ number_format($stat['qty_sold'], 2) }} kg</td>
              <td><strong>₱{{ number_format($stat['revenue'], 2) }}</strong></td>
              <td>₱{{ number_format($stat['capital_used'], 2) }}</td>
              <td class="{{ $stat['net_profit'] >= 0 ? 'profit-cell' : 'loss-cell' }}">
                ₱{{ number_format(abs($stat['net_profit']), 2) }}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="total-row">
              <td colspan="3"><strong>TOTAL</strong></td>
              <td>{{ number_format($totals['qty_sold'], 2) }} kg</td>
              <td>₱{{ number_format($totals['revenue'], 2) }}</td>
              <td>₱{{ number_format($totals['capital_used'], 2) }}</td>
              <td class="{{ $totals['net_profit'] >= 0 ? 'profit-cell' : 'loss-cell' }}">
                ₱{{ number_format(abs($totals['net_profit']), 2) }}
              </td>
            </tr>
          </tfoot>
        </table>
      @endif
    </div>

  </div>
</div>

</body>
</html>