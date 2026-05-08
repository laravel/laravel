<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin - Orders</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }

    /* ===== SIDEBAR ===== */
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

    /* ===== MAIN ===== */
    .main { margin-left: 240px; flex: 1; }
    .topbar { background: white; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .topbar h1 { font-size: 18px; font-weight: 800; color: #1a3a52; display: flex; align-items: center; gap: 10px; }
    .content { padding: 2rem; }

    /* ===== STATS ===== */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card { background: white; border-radius: 12px; padding: 1.1rem 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .stat-icon.blue   { background: #ddeef8; color: #2a7db5; }
    .stat-icon.yellow { background: #fff3cd; color: #b8860b; }
    .stat-icon.green  { background: #d4f5e9; color: #0a6640; }
    .stat-icon.red    { background: #fde8e8; color: #c0392b; }
    .stat-label { font-size: 11px; color: #6b7f8e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; }
    .stat-value { font-size: 22px; font-weight: 800; color: #1a3a52; }

    /* ===== FILTERS ===== */
    .filter-bar { display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .filter-btn { padding: 7px 16px; border-radius: 20px; border: 1.5px solid #d0dce8; background: white; font-size: 12px; font-weight: 700; color: #6b7f8e; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
    .filter-btn:hover { border-color: #2a7db5; color: #2a7db5; }
    .filter-btn.active { background: #2a7db5; border-color: #2a7db5; color: white; }
    .filter-btn .count { background: rgba(255,255,255,0.3); padding: 1px 7px; border-radius: 10px; font-size: 11px; }
    .filter-btn:not(.active) .count { background: #f0f4f8; color: #6b7f8e; }

    /* ===== TABLE ===== */
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
    .order-id { font-weight: 700; color: #1a3a52; font-size: 13px; }
    .customer-info strong { display: block; font-weight: 700; color: #1a3a52; font-size: 13px; }
    .customer-info span { font-size: 11px; color: #6b7f8e; }
    .product-list { font-size: 12px; color: #444; }
    .product-list span { display: block; }

    /* ===== BADGES ===== */
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .badge.pending   { background: #fff3cd; color: #856404; }
    .badge.confirmed { background: #cce5ff; color: #004085; }
    .badge.completed { background: #d4f5e9; color: #0a6640; }
    .badge.cancelled { background: #fde8e8; color: #c0392b; }

    /* ===== ACTION BUTTONS ===== */
    .actions { display: flex; gap: 5px; flex-wrap: nowrap; }
    .btn-action {
      padding: 5px 10px; border-radius: 7px; font-size: 11px; font-weight: 700;
      border: none; cursor: pointer; display: inline-flex; align-items: center;
      gap: 5px; white-space: nowrap; transition: opacity 0.2s, transform 0.15s;
    }
    .btn-action:hover:not(:disabled) { opacity: 0.85; transform: translateY(-1px); }
    .btn-action:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-confirm-act { background: #cce5ff; color: #004085; }
    .btn-complete-act { background: #d4f5e9; color: #0a6640; }
    .btn-cancel-act  { background: #fde8e8; color: #c0392b; }

    /* ===== EMPTY STATE ===== */
    .empty-state { padding: 3rem; text-align: center; color: #6b7f8e; }
    .empty-state i { font-size: 3rem; opacity: 0.25; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; }

    /* ===== MODAL ===== */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 1000;
      align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-card {
      background: white; border-radius: 18px; width: 100%; max-width: 420px;
      box-shadow: 0 16px 60px rgba(0,0,0,0.25); animation: slideUp 0.22s ease;
      overflow: hidden;
    }
    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      padding: 1.25rem 1.5rem;
      display: flex; align-items: center; gap: 12px;
    }
    .modal-header-icon {
      width: 44px; height: 44px; border-radius: 50%;
      background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; color: white; flex-shrink: 0;
    }
    .modal-header-text h3 { font-size: 16px; font-weight: 800; color: white; margin-bottom: 2px; }
    .modal-header-text p { font-size: 12px; color: rgba(255,255,255,0.8); }
    .modal-body-cancel { padding: 1.5rem; }
    .order-summary-box {
      background: #f8fafc; border-radius: 10px; padding: 12px 14px;
      margin-bottom: 1.1rem; border: 1px solid #e8eef4;
    }
    .order-summary-box .row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; }
    .order-summary-box .row:last-child { margin-bottom: 0; }
    .order-summary-box .row .lbl { color: #6b7f8e; font-weight: 600; }
    .order-summary-box .row .val { color: #1a3a52; font-weight: 700; }
    .reason-label { font-size: 11px; font-weight: 700; color: #6b7f8e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 7px; display: flex; align-items: center; gap: 5px; }
    
    textarea#cancelReason:focus { border-color: #e74c3c; box-shadow: 0 0 0 3px rgba(231,76,60,0.1); }
    .modal-footer { display: flex; gap: 8px; padding: 0 1.5rem 1.5rem; }
    .btn-modal-cancel {
      flex: 1; padding: 11px; background: #f0f4f8; color: #1a3a52;
      border: none; border-radius: 9px; font-size: 13px; font-weight: 700;
      cursor: pointer; transition: background 0.2s;
    }
    .btn-modal-cancel:hover { background: #dde5ee; }
    .btn-modal-confirm {
      flex: 1; padding: 11px; background: #e74c3c; color: white;
      border: none; border-radius: 9px; font-size: 13px; font-weight: 800;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
      transition: background 0.2s;
    }
    .btn-modal-confirm:hover { background: #c0392b; }

    /* ===== SPINNER ===== */
    .spinner { width: 16px; height: 16px; border: 2.5px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== TOAST ===== */
    .toast {
      position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
      background: #1a3a52; color: white; padding: 12px 18px;
      border-radius: 10px; font-size: 13px; font-weight: 600;
      display: flex; align-items: center; gap: 9px;
      box-shadow: 0 6px 24px rgba(0,0,0,0.2);
      transform: translateY(20px); opacity: 0;
      transition: all 0.3s;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.success { background: #27ae60; }
    .toast.error   { background: #e74c3c; }

    @media (max-width: 1024px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px)  { .main { margin-left: 0; } .sidebar { display: none; } .stats-row { grid-template-columns: repeat(2, 1fr); } }
  </style>
</head>
<body>

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <i class="fa-solid fa-fish"></i>
      <div>eIsda <span>Admin Panel</span></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
<div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
<div class="nav-item"><a href="{{ route('admin.orders') }}" class="active"><span class="nav-icon">📋</span> Orders</a></div>
<div class="nav-item"><a href="{{ route('admin.users') }}"><span class="nav-icon">👥</span> Users</a></div>
<div class="nav-item"><a href="{{ route('admin.history') }}"><span class="nav-icon">🕒</span> History</a></div>
<div class="nav-item"><a href="{{ route('admin.financial') }}"><span class="nav-icon">💰</span> Financial</a></div>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
      </form>
    </div>
  </aside>

  <!-- ===== MAIN ===== -->
  <div class="main">
    <div class="topbar">
      <h1><i class="fa-solid fa-clipboard-list" style="color:#2a7db5"></i> Orders Management</h1>
      <span style="font-size:12px;color:#6b7f8e;">{{ now()->format('F d, Y') }}</span>
    </div>

    <div class="content">

      <!-- STATS -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
          <div><div class="stat-label">Total Orders</div><div class="stat-value">{{ $orders->count() }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i class="fa-solid fa-clock"></i></div>
          <div><div class="stat-label">Pending</div><div class="stat-value">{{ $orders->where('status','pending')->count() }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div><div class="stat-label">Confirmed</div><div class="stat-value">{{ $orders->where('status','confirmed')->count() }}</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-xmark"></i></div>
          <div><div class="stat-label">Cancelled</div><div class="stat-value">{{ $orders->where('status','cancelled')->count() }}</div></div>
        </div>
      </div>

      <!-- FILTER TABS -->
      <div class="filter-bar">
        <button class="filter-btn active" onclick="filterOrders('all', this)">
          <i class="fa-solid fa-list"></i> All <span class="count">{{ $orders->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterOrders('pending', this)">
          <i class="fa-solid fa-clock"></i> Pending <span class="count">{{ $orders->where('status','pending')->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterOrders('confirmed', this)">
          <i class="fa-solid fa-thumbs-up"></i> Confirmed <span class="count">{{ $orders->where('status','confirmed')->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterOrders('completed', this)">
          <i class="fa-solid fa-circle-check"></i> Completed <span class="count">{{ $orders->where('status','completed')->count() }}</span>
        </button>
        <button class="filter-btn" onclick="filterOrders('cancelled', this)">
          <i class="fa-solid fa-ban"></i> Cancelled <span class="count">{{ $orders->where('status','cancelled')->count() }}</span>
        </button>
      </div>

      <!-- TABLE -->
      <div class="table-card">
        <div class="table-header">
          <h2><i class="fa-solid fa-table-list" style="color:#2a7db5;margin-right:6px"></i>Order List</h2>
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search order, customer..." oninput="searchOrders(this.value)"/>
          </div>
        </div>
        <div style="overflow-x:auto">
          <table id="ordersTable">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="ordersBody">
              @forelse($orders as $order)
              <tr data-status="{{ $order->status }}" data-search="{{ strtolower($order->customer_name . ' ' . $order->id) }}">
                <td><span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span><br><span style="font-size:11px;color:#6b7f8e;">{{ $order->created_at->format('M d, H:i') }}</span></td>
                <td>
                  <div class="customer-info">
                    <strong>{{ $order->customer_name }}</strong>
                    <span><i class="fa-solid fa-phone" style="font-size:9px"></i> {{ $order->customer_phone }}</span>
                    <span><i class="fa-solid fa-location-dot" style="font-size:9px"></i> {{ Str::limit($order->customer_address, 28) }}</span>
                  </div>
                </td>
                <td>
                  <div class="product-list">
                    @foreach($order->items as $item)
                    <span>{{ $item->product->name ?? 'N/A' }} × {{ $item->quantity }}kg</span>
                    @endforeach
                  </div>
                </td>
                <td><strong style="color:#2a7db5;font-size:13px">₱{{ number_format($order->total_amount, 2) }}</strong></td>
                <td style="font-size:12px;color:#444">{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                <td>
                  <span class="badge {{ $order->status }}">
                    @if($order->status === 'pending')   <i class="fa-solid fa-clock"></i> Pending
                    @elseif($order->status === 'confirmed') <i class="fa-solid fa-thumbs-up"></i> Confirmed
                    @elseif($order->status === 'completed') <i class="fa-solid fa-circle-check"></i> Completed
                    @elseif($order->status === 'cancelled') <i class="fa-solid fa-ban"></i> Cancelled
                    @endif
                  </span>
                </td>
                <td>
                  <div class="actions">
                    @if($order->status === 'pending')
                      <button class="btn-action btn-confirm-act" onclick="updateStatus({{ $order->id }}, 'confirmed')">
                        <i class="fa-solid fa-thumbs-up"></i> Confirm
                      </button>
                      <button class="btn-action btn-cancel-act" onclick="openCancelModal({{ $order->id }}, '{{ $order->customer_name }}', '{{ number_format($order->total_amount, 2) }}')">
                        <i class="fa-solid fa-ban"></i> Cancel
                      </button>
                    @elseif($order->status === 'confirmed')
                      <button class="btn-action btn-complete-act" onclick="updateStatus({{ $order->id }}, 'completed')">
                        <i class="fa-solid fa-circle-check"></i> Complete
                      </button>
                      <button class="btn-action btn-cancel-act" onclick="openCancelModal({{ $order->id }}, '{{ $order->customer_name }}', '{{ number_format($order->total_amount, 2) }}')">
                        <i class="fa-solid fa-ban"></i> Cancel
                      </button>
                    @elseif($order->status === 'completed')
                      <span style="font-size:11px;color:#0a6640;font-weight:600"><i class="fa-solid fa-circle-check"></i> Done</span>
                    @elseif($order->status === 'cancelled')
                      <span style="font-size:11px;color:#c0392b;font-weight:600"><i class="fa-solid fa-ban"></i> Cancelled</span>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-clipboard-list"></i><p>Walang orders pa.</p></div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== CANCEL MODAL ===== -->
  <div class="modal-overlay" id="cancelModal">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-header-icon"><i class="fa-solid fa-ban"></i></div>
        <div class="modal-header-text">
          <h3>Cancel Order</h3>
          <p>This action cannot be undone.</p>
        </div>
      </div>
      <div class="modal-body-cancel">
        <div class="order-summary-box" id="cancelOrderSummary"></div>
        <div class="reason-label"><i class="fa-solid fa-comment-dots"></i> Reason for Cancellation</div>
       
        <textarea id="cancelReason" placeholder="Ilagay ang dahilan ng cancellation..."></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn-modal-cancel" onclick="closeCancelModal()"><i class="fa-solid fa-arrow-left"></i> Go Back</button>
        <button class="btn-modal-confirm" id="cancelConfirmBtn" onclick="confirmCancel()">
          <i class="fa-solid fa-ban"></i> Confirm Cancel
        </button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast"></div>

  <script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let cancelOrderId = null;

    // ===== FILTER =====
    function filterOrders(status, btn) {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const rows = document.querySelectorAll('#ordersBody tr[data-status]');
      rows.forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
      });
    }

    // ===== SEARCH =====
    function searchOrders(q) {
      const val = q.toLowerCase();
      document.querySelectorAll('#ordersBody tr[data-status]').forEach(row => {
        row.style.display = row.dataset.search.includes(val) ? '' : 'none';
      });
    }

    // ===== UPDATE STATUS =====
    async function updateStatus(orderId, newStatus) {
      try {
        const response = await fetch(`/admin/orders/${orderId}/status`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
          body: JSON.stringify({ status: newStatus })
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Failed.');
        showToast(data.message || 'Status updated!', 'success');
        setTimeout(() => location.reload(), 900);
      } catch (err) {
        showToast('Error: ' + err.message, 'error');
      }
    }

    // ===== CANCEL MODAL =====
    function openCancelModal(orderId, customerName, total) {
      cancelOrderId = orderId;
      document.getElementById('cancelOrderSummary').innerHTML = `
        <div class="row"><span class="lbl">Order ID</span><span class="val">#${String(orderId).padStart(4,'0')}</span></div>
        <div class="row"><span class="lbl">Customer</span><span class="val">${customerName}</span></div>
        <div class="row"><span class="lbl">Amount</span><span class="val" style="color:#e74c3c">₱${total}</span></div>
      `;
      document.getElementById('cancelReason').value = '';
     
      document.getElementById('cancelModal').classList.add('active');
    }

    function closeCancelModal() {
      document.getElementById('cancelModal').classList.remove('active');
      cancelOrderId = null;
    }

   
    async function confirmCancel() {
      const reason = document.getElementById('cancelReason').value.trim();
      if (!reason) { showToast('Pakilagay ang dahilan ng cancellation.', 'error'); return; }
      const btn = document.getElementById('cancelConfirmBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Cancelling...';
      try {
        const response = await fetch(`/admin/orders/${cancelOrderId}/cancel`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
          body: JSON.stringify({ reason })
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Failed.');
        closeCancelModal();
        showToast('Order cancelled successfully.', 'success');
        setTimeout(() => location.reload(), 900);
      } catch (err) {
        showToast('Error: ' + err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-ban"></i> Confirm Cancel';
      }
    }

    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) closeCancelModal();
    });

    // ===== TOAST =====
    function showToast(msg, type = '') {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.className = 'toast ' + type + ' show';
      setTimeout(() => t.classList.remove('show'), 3000);
    }
  </script>

</body>
</html>