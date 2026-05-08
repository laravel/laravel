<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda – Order History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    :root {
      --navy:  #0d3b5e;
      --blue:  #2a7db5;
      --blue2: #1a5a8a;
      --teal:  #0e9e8e;
      --light: #f0f6fc;
      --card:  #ffffff;
      --border:#d9e6f0;
      --text:  #1e3a50;
      --muted: #7a94a8;
      --red:   #e53e3e;
      --green: #16a34a;
      --amber: #d97706;
      --radius:14px;
      --shadow:0 2px 12px rgba(13,59,94,.09);
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: var(--light);
      min-height: 100vh;
      color: var(--text);
    }

    /* ── NAV ── */
    nav {
      background: linear-gradient(90deg, var(--blue), var(--navy));
      height: 60px;
      padding: 0 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 10px rgba(0,0,0,.18);
      position: sticky;
      top: 0;
      z-index: 200;
    }
    .nav-logo {
      color:#fff; font-size:19px; font-weight:800;
      text-decoration:none; display:flex; align-items:center; gap:8px;
      letter-spacing:-.3px;
    }
    .nav-links { display:flex; gap:.25rem; }
    .nav-links a {
      color:rgba(255,255,255,.82); text-decoration:none;
      padding:7px 13px; border-radius:8px; font-size:13px; font-weight:600;
      display:flex; align-items:center; gap:6px;
      transition: background .18s, color .18s;
    }
    .nav-links a:hover { background:rgba(255,255,255,.15); color:#fff; }
    .nav-links a.active { background:rgba(255,255,255,.22); color:#fff; }
    .nav-right { display:flex; align-items:center; gap:.6rem; }
    .nav-icon-btn {
      position:relative; color:#fff; text-decoration:none;
      font-size:19px; display:flex; align-items:center;
      padding:6px 8px; border-radius:8px; transition:background .18s;
    }
    .nav-icon-btn:hover { background:rgba(255,255,255,.15); }
    .nav-badge {
      position:absolute; top:1px; right:1px;
      background:#e74c3c; color:#fff; font-size:9px; font-weight:800;
      width:16px; height:16px; border-radius:50%;
      display:none; align-items:center; justify-content:center;
      border:1.5px solid var(--navy);
    }
    .btn-logout {
      background:rgba(255,255,255,.13); color:#fff;
      border:1px solid rgba(255,255,255,.28); border-radius:8px;
      padding:6px 13px; font-size:13px; font-weight:600; cursor:pointer;
      display:flex; align-items:center; gap:6px; font-family:inherit;
      transition:background .18s;
    }
    .btn-logout:hover { background:rgba(255,255,255,.24); }

    /* ── PAGE HEADER ── */
    .page-header {
      background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
      color:#fff; padding:2.2rem 1.5rem 2rem; text-align:center;
    }
    .page-header h1 {
      font-size:1.65rem; font-weight:800;
      display:flex; align-items:center; justify-content:center; gap:10px;
    }
    .page-header p { opacity:.8; margin-top:.3rem; font-size:13.5px; }

    /* ── STATS BAR ── */
    .stats-bar {
      max-width:820px; margin:1.4rem auto 0; padding:0 1rem;
      display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:.8rem;
    }
    .stat-card {
      background:#fff; border-radius:12px; border:1px solid var(--border);
      padding:.9rem 1rem; text-align:center; box-shadow:var(--shadow);
    }
    .stat-icon { font-size:1.4rem; margin-bottom:.35rem; }
    .stat-value { font-size:1.35rem; font-weight:800; color:var(--navy); line-height:1; }
    .stat-label { font-size:11.5px; color:var(--muted); margin-top:.25rem; font-weight:600; }

    /* ── FILTER BAR ── */
    .filter-wrap {
      max-width:820px; margin:1rem auto 0; padding:0 1rem;
      display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;
    }
    .search-wrap { flex:1; min-width:180px; position:relative; }
    .search-wrap i {
      position:absolute; left:11px; top:50%; transform:translateY(-50%);
      color:var(--muted); font-size:14px; pointer-events:none;
    }
    .search-wrap input {
      width:100%; padding:9px 12px 9px 34px;
      border:1.5px solid var(--border); border-radius:9px;
      font-size:13px; font-family:inherit; color:var(--text);
      background:#fff; outline:none; transition:border-color .18s;
    }
    .search-wrap input:focus { border-color:var(--blue); }
    .filter-select {
      padding:9px 30px 9px 12px;
      border:1.5px solid var(--border); border-radius:9px;
      font-size:13px; font-family:inherit; color:var(--text);
      background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath fill='%237a94a8' d='M6 8L0 0h12z'/%3E%3C/svg%3E") no-repeat right 10px center / 10px;
      appearance:none; cursor:pointer; outline:none; transition:border-color .18s;
    }
    .filter-select:focus { border-color:var(--blue); }
    .result-count { font-size:12.5px; color:var(--muted); font-weight:600; white-space:nowrap; }

    /* ── TABS ── */
    .tabs-wrap {
      max-width:820px; margin:.85rem auto 0;
      background:#fff;
      border-top:1px solid var(--border);
      border-bottom:2px solid var(--border);
      display:flex;
    }
    .tab-btn {
      flex:1; padding:13px 10px;
      border:none; background:transparent; cursor:pointer;
      font-family:inherit; font-size:13.5px; font-weight:700; color:var(--muted);
      display:flex; align-items:center; justify-content:center; gap:7px;
      border-bottom:3px solid transparent; margin-bottom:-2px;
      transition:color .18s, border-color .18s;
    }
    .tab-btn:hover { color:var(--blue); }
    .tab-btn.active { color:var(--blue); border-bottom-color:var(--blue); }
    .tab-count {
      background:var(--border); color:var(--text); font-size:10px; font-weight:800;
      padding:2px 7px; border-radius:20px; transition:background .18s, color .18s;
    }
    .tab-btn.active .tab-count { background:var(--blue); color:#fff; }

    /* ── PANELS ── */
    .panel { display:none; }
    .panel.active { display:block; }
    .container { max-width:820px; margin:0 auto; padding:1.75rem 1rem 3rem; }

    /* ── ORDER CARD ── */
    .order-card {
      background:var(--card); border-radius:var(--radius);
      box-shadow:var(--shadow); overflow:hidden;
      margin-bottom:1.1rem; border:1px solid var(--border);
      transition:box-shadow .18s;
    }
    .order-card:hover { box-shadow:0 4px 22px rgba(13,59,94,.13); }
    .order-card-head {
      display:flex; align-items:center; justify-content:space-between;
      padding:.8rem 1.1rem; border-bottom:1px solid var(--border);
      background:#f7fbff; flex-wrap:wrap; gap:.4rem;
    }
    .order-id { font-size:13px; font-weight:800; color:var(--navy); }
    .order-date-label { font-size:11.5px; color:var(--muted); display:flex; align-items:center; gap:5px; }
    .status-pill {
      display:inline-flex; align-items:center; gap:5px;
      padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700;
    }
    .status-pill.completed { background:#e8f0fe; color:#3730a3; }
    .status-pill.cancelled { background:#fff0f0; color:var(--red); }

    .order-item {
      display:flex; align-items:center; gap:1rem;
      padding:.9rem 1.1rem; border-bottom:1px dashed var(--border);
    }
    .order-item:last-of-type { border-bottom:none; }
    .item-img {
      width:72px; height:72px; border-radius:10px;
      object-fit:cover; flex-shrink:0; background:#ddeef8;
    }
    .item-img-placeholder {
      width:72px; height:72px; border-radius:10px; flex-shrink:0;
      background:linear-gradient(135deg,#ddeef8,#c0dcf0);
      display:flex; align-items:center; justify-content:center;
      font-size:1.7rem; color:var(--blue2);
    }
    .item-info { flex:1; min-width:0; }
    .item-name { font-size:14px; font-weight:700; color:var(--text); margin-bottom:3px; }
    .item-meta { font-size:12px; color:var(--muted); }
    .item-price { font-size:15px; font-weight:800; color:var(--blue); white-space:nowrap; }

    .order-card-foot {
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem;
      padding:.8rem 1.1rem; border-top:1px solid var(--border); background:#f7fbff;
    }
    .foot-meta { font-size:12px; color:var(--muted); display:flex; align-items:center; gap:6px; }
    .foot-total { font-size:14px; font-weight:800; color:var(--navy); }
    .foot-total span { color:var(--blue); }
    .btn-del {
      display:inline-flex; align-items:center; gap:5px;
      padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700;
      border:1.5px solid #fca5a5; background:#fff5f5; color:var(--red);
      cursor:pointer; font-family:inherit; transition:background .15s, border-color .15s;
    }
    .btn-del:hover { background:#fee2e2; border-color:var(--red); }

    /* ── EMPTY STATE ── */
    .empty-state { text-align:center; padding:4rem 1rem; color:var(--muted); }
    .empty-state i { font-size:3.5rem; color:var(--border); margin-bottom:1rem; display:block; }
    .empty-state h3 { font-size:17px; font-weight:800; color:var(--text); margin-bottom:.4rem; }
    .empty-state p { font-size:13px; }
    .btn-shop-now {
      display:inline-flex; align-items:center; gap:7px;
      margin-top:1.2rem; padding:10px 22px;
      background:var(--blue); color:#fff; border-radius:10px;
      font-size:13.5px; font-weight:700; text-decoration:none; transition:background .18s;
    }
    .btn-shop-now:hover { background:var(--blue2); }

    /* ── DELETE MODAL ── */
    .modal-overlay {
      display:none; position:fixed; inset:0;
      background:rgba(0,0,0,.45); z-index:500;
      align-items:center; justify-content:center;
    }
    .modal-overlay.active { display:flex; }
    .modal-box {
      background:#fff; border-radius:16px;
      padding:2rem 1.75rem; max-width:380px; width:92%;
      box-shadow:0 10px 50px rgba(0,0,0,.2);
      text-align:center; animation:popIn .22s ease;
    }
    @keyframes popIn { from{transform:scale(.88);opacity:0} to{transform:scale(1);opacity:1} }
    .modal-icon { font-size:2.4rem; margin-bottom:.7rem; }
    .modal-title { font-size:1.1rem; font-weight:800; color:var(--text); margin-bottom:.35rem; }
    .modal-msg { font-size:13px; color:var(--muted); margin-bottom:1.5rem; line-height:1.55; }
    .modal-actions { display:flex; gap:.75rem; justify-content:center; }
    .btn-cancel {
      padding:9px 22px; border:1.5px solid var(--border); border-radius:9px;
      background:#fff; color:var(--text); font-size:13px; font-weight:700;
      cursor:pointer; font-family:inherit; transition:background .15s;
    }
    .btn-cancel:hover { background:#f3f4f6; }
    .btn-confirm-del {
      padding:9px 22px; border:none; border-radius:9px;
      background:var(--red); color:#fff; font-size:13px; font-weight:700;
      cursor:pointer; font-family:inherit;
      display:inline-flex; align-items:center; gap:6px; transition:background .15s;
    }
    .btn-confirm-del:hover { background:#c53030; }

    /* ── RESPONSIVE ── */
    @media(max-width:540px) {
      .nav-links { display:none; }
      .page-header h1 { font-size:1.3rem; }
      .stats-bar { grid-template-columns:repeat(2,1fr); }
      .item-img, .item-img-placeholder { width:58px; height:58px; }
      .tab-btn { font-size:12px; padding:12px 4px; gap:4px; }
    }
  </style>
</head>
<body>

  <!-- NAV -->
  <nav>
    <a class="nav-logo" href="#"><i class="fa-solid fa-fish"></i> eIsda</a>
    <div class="nav-links">
      <a href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Shop</a>
      <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
      {{-- ✅ FIXED: changed fa-box-open to fa-clipboard-list to match orders.blade.php --}}
      <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
      <a href="{{ route('customer.history') }}" class="active"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    </div>
    <div class="nav-right">
      <a href="#" class="nav-icon-btn" id="notifBtn">
        <i class="fa-solid fa-bell"></i>
        <span class="nav-badge" id="notifBadge"></span>
      </a>
      <a href="{{ route('customer.cart') }}" class="nav-icon-btn">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="nav-badge" id="cartBadge"></span>
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
          <i class="fa-solid fa-right-from-bracket"></i> Log Out
        </button>
      </form>
    </div>
  </nav>

  <!-- PAGE HEADER -->
  <div class="page-header">
    <h1><i class="fa-solid fa-clock-rotate-left"></i> Order History</h1>
    <p>All your past seafood orders in one place</p>
  </div>

  <!-- STATS BAR -->
  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-icon">📦</div>
      <div class="stat-value">{{ $completedOrders->count() + $cancelledOrders->count() }}</div>
      <div class="stat-label">Total Orders</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-value" style="color:var(--green);">{{ $completedOrders->count() }}</div>
      <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">❌</div>
      <div class="stat-value" style="color:var(--red);">{{ $cancelledOrders->count() }}</div>
      <div class="stat-label">Cancelled</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-value" style="color:var(--blue); font-size:1.1rem;">₱{{ number_format($completedOrders->sum('total_amount')) }}</div>
      <div class="stat-label">Total Spent</div>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-wrap">
    <div class="search-wrap">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="searchInput" placeholder="Search by order # or product name…" oninput="applyFilters()"/>
    </div>
    <select class="filter-select" id="sortSelect" onchange="applyFilters()">
      <option value="newest">Newest First</option>
      <option value="oldest">Oldest First</option>
      <option value="highest">Highest Amount</option>
      <option value="lowest">Lowest Amount</option>
    </select>
    <span class="result-count" id="resultCount"></span>
  </div>

  <!-- TABS -->
  <div class="tabs-wrap">
    <button class="tab-btn active" onclick="switchTab('all', this)">
      <i class="fa-solid fa-list"></i> All
      <span class="tab-count" id="cnt-all">{{ $completedOrders->count() + $cancelledOrders->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('completed', this)">
      <i class="fa-solid fa-flag-checkered"></i> Completed
      <span class="tab-count" id="cnt-completed">{{ $completedOrders->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('cancelled', this)">
      <i class="fa-solid fa-ban"></i> Cancelled
      <span class="tab-count" id="cnt-cancelled">{{ $cancelledOrders->count() }}</span>
    </button>
  </div>

  <!-- PANELS -->
  <div class="container">

    {{-- ── ALL ── --}}
    <div class="panel active" id="panel-all">
      @php $allOrders = $completedOrders->merge($cancelledOrders)->sortByDesc('created_at'); @endphp
      @forelse($allOrders as $order)
        <div class="order-card" data-date="{{ $order->created_at }}" data-amount="{{ $order->total_amount }}">
          <div class="order-card-head">
            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
              {{-- ✅ FIXED: removed fa-hashtag icon, added str_pad to match orders.blade.php --}}
              <span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
              <span class="order-date-label"><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</span>
            </div>
            @if($order->status === 'completed')
              <span class="status-pill completed"><i class="fa-solid fa-flag-checkered"></i> Completed</span>
            @else
              <span class="status-pill cancelled"><i class="fa-solid fa-ban"></i> Cancelled</span>
            @endif
          </div>
          @foreach($order->items as $item)
          <div class="order-item">
            @if($item->product && $item->product->image)
              <img class="item-img" src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}"/>
            @else
              <div class="item-img-placeholder"><i class="fa-solid fa-fish"></i></div>
            @endif
            <div class="item-info">
              <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
              <div class="item-meta">{{ $item->quantity }} kg &nbsp;·&nbsp; ₱{{ number_format($item->product->price ?? 0) }}/kg</div>
            </div>
            <div class="item-price">₱{{ number_format($item->quantity * ($item->product->price ?? 0)) }}</div>
          </div>
          @endforeach
          <div class="order-card-foot">
            <div>
              <div class="foot-meta">
                <i class="fa-regular fa-calendar-check"></i>
                @if($order->status === 'completed')
                  Picked up: {{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}
                @else
                  Ordered: {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                @endif
              </div>
              <div class="foot-total">Total: <span>₱{{ number_format($order->total_amount) }}</span></div>
            </div>
            <button class="btn-del" onclick="confirmDelete({{ $order->id }})">
              <i class="fa-solid fa-trash"></i> Remove
            </button>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <h3>No Order History Yet</h3>
          <p>Your completed and cancelled orders will appear here.</p>
          <a href="{{ route('customer.shop') }}" class="btn-shop-now">
            <i class="fa-solid fa-store"></i> Start Shopping
          </a>
        </div>
      @endforelse
    </div>

    {{-- ── COMPLETED ── --}}
    <div class="panel" id="panel-completed">
      @forelse($completedOrders as $order)
        <div class="order-card" data-date="{{ $order->created_at }}" data-amount="{{ $order->total_amount }}">
          <div class="order-card-head">
            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
              {{-- ✅ FIXED: removed fa-hashtag icon, added str_pad to match orders.blade.php --}}
              <span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
              <span class="order-date-label"><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</span>
            </div>
            <span class="status-pill completed"><i class="fa-solid fa-flag-checkered"></i> Completed</span>
          </div>
          @foreach($order->items as $item)
          <div class="order-item">
            @if($item->product && $item->product->image)
              <img class="item-img" src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}"/>
            @else
              <div class="item-img-placeholder"><i class="fa-solid fa-fish"></i></div>
            @endif
            <div class="item-info">
              <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
              <div class="item-meta">{{ $item->quantity }} kg &nbsp;·&nbsp; ₱{{ number_format($item->product->price ?? 0) }}/kg</div>
            </div>
            <div class="item-price">₱{{ number_format($item->quantity * ($item->product->price ?? 0)) }}</div>
          </div>
          @endforeach
          <div class="order-card-foot">
            <div>
              <div class="foot-meta"><i class="fa-regular fa-calendar-check"></i> Picked up: {{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</div>
              <div class="foot-total">Total: <span>₱{{ number_format($order->total_amount) }}</span></div>
            </div>
            <button class="btn-del" onclick="confirmDelete({{ $order->id }})">
              <i class="fa-solid fa-trash"></i> Remove
            </button>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class="fa-solid fa-flag-checkered"></i>
          <h3>No Completed Orders</h3>
          <p>Orders you have successfully received will appear here.</p>
        </div>
      @endforelse
    </div>

    {{-- ── CANCELLED ── --}}
    <div class="panel" id="panel-cancelled">
      @forelse($cancelledOrders as $order)
        <div class="order-card" data-date="{{ $order->created_at }}" data-amount="{{ $order->total_amount }}">
          <div class="order-card-head">
            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
              {{-- ✅ FIXED: removed fa-hashtag icon, added str_pad to match orders.blade.php --}}
              <span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
              <span class="order-date-label"><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</span>
            </div>
            <span class="status-pill cancelled"><i class="fa-solid fa-ban"></i> Cancelled</span>
          </div>
          @foreach($order->items as $item)
          <div class="order-item">
            @if($item->product && $item->product->image)
              <img class="item-img" src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}"/>
            @else
              <div class="item-img-placeholder"><i class="fa-solid fa-fish"></i></div>
            @endif
            <div class="item-info">
              <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
              <div class="item-meta">{{ $item->quantity }} kg &nbsp;·&nbsp; ₱{{ number_format($item->product->price ?? 0) }}/kg</div>
            </div>
            <div class="item-price">₱{{ number_format($item->quantity * ($item->product->price ?? 0)) }}</div>
          </div>
          @endforeach
          <div class="order-card-foot">
            <div>
              <div class="foot-meta"><i class="fa-regular fa-calendar"></i> Ordered: {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</div>
              <div class="foot-total">Total: <span>₱{{ number_format($order->total_amount) }}</span></div>
            </div>
            <button class="btn-del" onclick="confirmDelete({{ $order->id }})">
              <i class="fa-solid fa-trash"></i> Remove
            </button>
          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class="fa-solid fa-ban"></i>
          <h3>No Cancelled Orders</h3>
          <p>You have no cancelled orders. Great job!</p>
        </div>
      @endforelse
    </div>

  </div><!-- /container -->

  <!-- DELETE MODAL -->
  <div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
      <div class="modal-icon">🗑️</div>
      <div class="modal-title">Remove from History</div>
      <div class="modal-msg">Remove this order from your history? This cannot be undone.</div>
      <div class="modal-actions">
        <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
        <form id="deleteForm" method="POST" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-confirm-del">
            <i class="fa-solid fa-trash"></i> Yes, Remove
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // ── Cart Badge ──
    const cartData = JSON.parse(localStorage.getItem('eisda_cart') || '[]');
    const cartBadge = document.getElementById('cartBadge');
    if (cartData.length > 0) { cartBadge.textContent = cartData.length; cartBadge.style.display = 'flex'; }

    // ── Tabs ──
    let currentTab = 'all';
    function switchTab(tab, btn) {
      currentTab = tab;
      document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('panel-' + tab).classList.add('active');
      btn.classList.add('active');
      applyFilters();
    }

    // ── Search & Sort ──
    function applyFilters() {
      const q = document.getElementById('searchInput').value.toLowerCase().trim();
      const sort = document.getElementById('sortSelect').value;
      const panel = document.getElementById('panel-' + currentTab);
      const cards = Array.from(panel.querySelectorAll('.order-card'));
      let visible = 0;

      cards.forEach(card => {
        const match = !q || card.textContent.toLowerCase().includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
      });

      const parent = cards[0]?.parentElement;
      if (parent) {
        cards.filter(c => c.style.display !== 'none').sort((a, b) => {
          const dA = new Date(a.dataset.date || 0), dB = new Date(b.dataset.date || 0);
          const aA = parseFloat(a.dataset.amount || 0), aB = parseFloat(b.dataset.amount || 0);
          if (sort === 'newest')  return dB - dA;
          if (sort === 'oldest')  return dA - dB;
          if (sort === 'highest') return aB - aA;
          if (sort === 'lowest')  return aA - aB;
          return 0;
        }).forEach(c => parent.appendChild(c));
      }

      const rc = document.getElementById('resultCount');
      rc.textContent = q ? `${visible} result${visible !== 1 ? 's' : ''} found` : '';
    }

    // ── Delete Modal ──
    function confirmDelete(orderId) {
      document.getElementById('deleteForm').action = `/history/${orderId}`;
      document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.remove('active');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
      if (e.target === this) closeDeleteModal();
    });

    applyFilters();
  </script>
</body>
</html>