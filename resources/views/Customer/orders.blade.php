{{-- resources/views/Customer/orders.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda – My Orders</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --navy: #0d2b45;
      --blue: #2a7db5;
      --blue-light: #e8f4fd;
      --blue-mid: #1a5a8a;
      --green: #16a34a;
      --green-light: #dcfce7;
      --red: #dc2626;
      --red-light: #fee2e2;
      --orange: #ea580c;
      --orange-light: #fff7ed;
      --yellow: #d97706;
      --yellow-light: #fef3c7;
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-900: #0f172a;
      --radius: 12px;
      --shadow: 0 2px 12px rgba(0,0,0,0.07);
      --shadow-lg: 0 8px 40px rgba(0,0,0,0.15);
    }

    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--gray-100); min-height:100vh; color:var(--gray-700); }

    /* ═══ NAV ═══ */
    nav {
      background: linear-gradient(90deg,#2a7db5,#0d2b45);
      padding: 0 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
      height: 62px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.2);
      position: sticky; top:0; z-index:100;
    }
    .nav-logo { color:#fff; font-size:20px; font-weight:800; text-decoration:none; display:flex; align-items:center; gap:8px; }
    .nav-logo i { color:#93d3f5; }
    .nav-links { display:flex; gap:0.25rem; }
    .nav-links a {
      color:rgba(255,255,255,0.8); text-decoration:none;
      padding:8px 14px; border-radius:8px; font-size:13px; font-weight:500;
      transition:all 0.2s; display:flex; align-items:center; gap:6px;
    }
    .nav-links a:hover { background:rgba(255,255,255,0.15); color:#fff; }
    .nav-links a.active { background:rgba(255,255,255,0.2); color:#fff; font-weight:700; }
    .nav-right { display:flex; align-items:center; gap:0.6rem; }
    .notif-btn {
      position:relative; color:#fff; font-size:18px; padding:8px; border-radius:8px;
      background:rgba(255,255,255,0.15); cursor:pointer; border:none;
      display:flex; align-items:center; justify-content:center; transition:background 0.2s;
    }
    .notif-btn:hover { background:rgba(255,255,255,0.25); }
    .notif-badge {
      position:absolute; top:-2px; right:-2px;
      background:#ef4444; color:#fff; font-size:9px; font-weight:800;
      min-width:16px; height:16px; border-radius:20px; padding:0 4px;
      display:none; align-items:center; justify-content:center; border:2px solid #1a5a8a;
    }
    .notif-badge.show { display:flex; }
    .cart-btn {
      position:relative; color:#fff; text-decoration:none; font-size:18px; padding:8px;
      border-radius:8px; background:rgba(255,255,255,0.2);
      display:flex; align-items:center; justify-content:center; transition:background 0.2s;
    }
    .cart-btn:hover { background:rgba(255,255,255,0.3); }
    .btn-logout {
      background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.25);
      border-radius:8px; padding:7px 13px; font-size:13px; cursor:pointer;
      display:flex; align-items:center; gap:6px; font-family:inherit; transition:background 0.2s;
    }
    .btn-logout:hover { background:rgba(255,255,255,0.22); }
    .hamburger { display:none; color:#fff; font-size:22px; background:none; border:none; cursor:pointer; padding:6px; }
    .mobile-menu {
      display:none; flex-direction:column; background:var(--navy);
      padding:0.75rem 1rem; gap:0.25rem; border-bottom:2px solid var(--blue);
    }
    .mobile-menu.open { display:flex; }
    .mobile-menu a {
      color:rgba(255,255,255,0.85); text-decoration:none;
      padding:10px 14px; border-radius:8px; font-size:14px; font-weight:500;
      display:flex; align-items:center; gap:8px;
    }
    .mobile-menu a:hover { background:rgba(255,255,255,0.1); color:#fff; }

    /* ═══ PAGE HEADER ═══ */
    .page-header {
      background:linear-gradient(135deg,#2a7db5,#0d2b45);
      color:#fff; padding:1.75rem 1.5rem; text-align:center;
    }
    .page-header h1 { font-size:1.5rem; font-weight:800; display:flex; align-items:center; justify-content:center; gap:10px; }
    .page-header p { opacity:0.75; margin-top:4px; font-size:13px; }

    /* ═══ CONTAINER ═══ */
    .container { max-width:760px; margin:0 auto; padding:1.5rem 1rem; }

    /* ═══ FILTER TABS ═══ */
    .filter-tabs {
      display:flex; gap:0.4rem; margin-bottom:1.25rem;
      overflow-x:auto; padding-bottom:4px;
    }
    .filter-tabs::-webkit-scrollbar { height:3px; }
    .filter-tabs::-webkit-scrollbar-thumb { background:var(--gray-300); border-radius:4px; }
    .tab-btn {
      padding:7px 16px; border-radius:20px; border:1.5px solid var(--gray-200);
      background:white; font-size:12px; font-weight:700; color:var(--gray-600);
      cursor:pointer; white-space:nowrap; transition:all 0.2s; font-family:inherit;
      display:flex; align-items:center; gap:5px;
    }
    .tab-btn:hover { border-color:var(--blue); color:var(--blue); }
    .tab-btn.active { background:var(--blue); border-color:var(--blue); color:white; }
    .tab-count {
      background:rgba(255,255,255,0.25); padding:1px 6px;
      border-radius:10px; font-size:10px;
    }
    .tab-btn:not(.active) .tab-count { background:var(--gray-100); color:var(--gray-400); }

    /* ═══ EMPTY STATE ═══ */
    .empty-state {
      background:white; border-radius:var(--radius); padding:3.5rem 2rem;
      text-align:center; box-shadow:var(--shadow);
    }
    .empty-icon { font-size:3.5rem; color:var(--gray-300); margin-bottom:1rem; }
    .empty-state h3 { font-size:17px; font-weight:700; color:var(--navy); margin-bottom:6px; }
    .empty-state p { font-size:13px; color:var(--gray-400); margin-bottom:1.5rem; }
    .btn-shop {
      background:var(--blue); color:#fff; border:none; border-radius:9px;
      padding:10px 22px; font-size:13px; font-weight:700; cursor:pointer;
      text-decoration:none; display:inline-flex; align-items:center; gap:7px; font-family:inherit;
    }
    .btn-shop:hover { background:var(--blue-mid); }

    /* ═══ ORDER CARDS ═══ */
    .orders-list { display:flex; flex-direction:column; gap:0.75rem; }

    .order-card {
      background:white; border-radius:var(--radius);
      box-shadow:var(--shadow); overflow:hidden;
      border:1.5px solid var(--gray-200);
      transition:box-shadow 0.2s;
    }
    .order-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.1); }

    /* Card Header */
    .order-card-header {
      padding:12px 16px;
      display:flex; align-items:center; justify-content:space-between;
      border-bottom:1px solid var(--gray-100);
      background:var(--gray-50);
    }
    .order-header-left { display:flex; align-items:center; gap:10px; }
    .order-id { font-size:13px; font-weight:800; color:var(--navy); }
    .order-date { font-size:11px; color:var(--gray-400); }

    /* Status Badge */
    .status-badge {
      display:inline-flex; align-items:center; gap:5px;
      padding:4px 11px; border-radius:20px; font-size:11px; font-weight:700;
    }
    .status-badge.pending   { background:var(--yellow-light); color:var(--yellow); }
    .status-badge.confirmed { background:var(--blue-light); color:var(--blue-mid); }
    .status-badge.completed { background:var(--green-light); color:var(--green); }
    .status-badge.cancelled { background:var(--red-light); color:var(--red); }

    /* Card Body */
    .order-card-body { padding:14px 16px; }

    .order-items-list { display:flex; flex-direction:column; gap:10px; margin-bottom:12px; }
    .order-item-row {
      display:flex; align-items:center; gap:12px;
    }
    .item-img {
      width:52px; height:52px; border-radius:8px; flex-shrink:0;
      background:linear-gradient(135deg,var(--blue-light),var(--gray-100));
      display:flex; align-items:center; justify-content:center;
      font-size:1.4rem; color:var(--blue);
      border:1px solid var(--gray-200); overflow:hidden;
    }
    .item-img img { width:100%; height:100%; object-fit:cover; border-radius:8px; }
    .item-details { flex:1; min-width:0; }
    .item-name { font-size:13px; font-weight:700; color:var(--navy); }
    .item-meta { font-size:11px; color:var(--gray-400); margin-top:2px; }
    .item-subtotal { font-size:13px; font-weight:800; color:var(--navy); white-space:nowrap; }

    /* Order Footer */
    .order-card-footer {
      padding:10px 16px;
      border-top:1px solid var(--gray-100);
      display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
    }
    .order-total-label { font-size:11px; color:var(--gray-400); }
    .order-total-amount { font-size:17px; font-weight:800; color:var(--red); }

    .order-actions { display:flex; gap:7px; }

    .btn-cancel-order {
      background:var(--red-light); color:var(--red); border:none;
      border-radius:8px; padding:7px 14px; font-size:12px; font-weight:700;
      cursor:pointer; display:flex; align-items:center; gap:5px; font-family:inherit;
      transition:background 0.2s;
    }
    .btn-cancel-order:hover { background:#fecaca; }

    /* Cancel reason display */
    .cancel-reason-box {
      margin:0 16px 12px;
      background:var(--red-light); border-radius:8px; padding:10px 12px;
      border-left:3px solid var(--red);
      display:flex; gap:8px; align-items:flex-start;
      font-size:12px; color:var(--red);
    }
    .cancel-reason-box i { margin-top:1px; flex-shrink:0; }

    /* Pickup info */
    .pickup-info {
      display:flex; align-items:center; gap:6px;
      font-size:11px; color:var(--gray-600);
      background:var(--yellow-light); border-radius:7px;
      padding:6px 10px; margin:0 16px 12px;
      border:1px solid #fde68a;
    }
    .pickup-info i { color:var(--yellow); }

    /* ═══ CANCEL MODAL ═══ */
    .modal-overlay {
      display:none; position:fixed; inset:0;
      background:rgba(0,0,0,0.55); z-index:1000;
      align-items:flex-end; justify-content:center;
      padding:0;
    }
    .modal-overlay.open { display:flex; }

    .cancel-modal {
      background:white;
      border-radius:20px 20px 0 0;
      width:100%; max-width:540px;
      box-shadow:var(--shadow-lg);
      animation:slideUp 0.28s cubic-bezier(0.34,1.56,0.64,1);
      overflow:hidden;
    }
    @keyframes slideUp {
      from { transform:translateY(100%); opacity:0; }
      to   { transform:translateY(0); opacity:1; }
    }

    .modal-handle {
      width:40px; height:4px; background:var(--gray-300);
      border-radius:4px; margin:12px auto 0;
    }

    .modal-header-cancel {
      padding:1rem 1.5rem 0.75rem;
      text-align:center;
    }
    .modal-cancel-icon {
      width:56px; height:56px; border-radius:50%;
      background:var(--red-light); color:var(--red);
      display:flex; align-items:center; justify-content:center;
      font-size:1.4rem; margin:0 auto 10px;
    }
    .modal-cancel-title { font-size:16px; font-weight:800; color:var(--gray-900); margin-bottom:3px; }
    .modal-cancel-sub { font-size:12px; color:var(--gray-400); }

    .modal-order-summary {
      margin:0 1.5rem 1rem;
      background:var(--gray-50); border-radius:10px;
      padding:10px 14px; border:1px solid var(--gray-200);
    }
    .summary-row { display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px; }
    .summary-row:last-child { margin-bottom:0; }
    .summary-lbl { color:var(--gray-400); font-weight:600; }
    .summary-val { color:var(--gray-900); font-weight:700; }

    .reason-section { padding:0 1.5rem 0.75rem; }
    .reason-label {
      font-size:11px; font-weight:700; color:var(--gray-600);
      text-transform:uppercase; letter-spacing:0.05em;
      margin-bottom:8px; display:flex; align-items:center; gap:5px;
    }
    .reason-chips { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px; }
    .reason-chip {
      padding:6px 12px; border-radius:20px;
      border:1.5px solid var(--gray-200); background:white;
      font-size:12px; font-weight:600; color:var(--gray-600);
      cursor:pointer; font-family:inherit; transition:all 0.18s;
    }
    .reason-chip:hover { border-color:var(--red); color:var(--red); }
    .reason-chip.selected { background:var(--red-light); border-color:var(--red); color:var(--red); }

    .reason-textarea {
      width:100%; padding:10px 12px;
      border:1.5px solid var(--gray-200); border-radius:9px;
      font-size:13px; font-family:inherit; color:var(--gray-900);
      resize:none; height:80px; outline:none;
      transition:border-color 0.2s, box-shadow 0.2s;
    }
    .reason-textarea:focus { border-color:var(--red); box-shadow:0 0 0 3px rgba(220,38,38,0.1); }
    .char-count { font-size:11px; color:var(--gray-400); text-align:right; margin-top:4px; }

    .modal-footer-cancel {
      padding:0.75rem 1.5rem 1.5rem;
      display:flex; gap:8px;
    }
    .btn-back {
      flex:1; padding:11px; background:var(--gray-100); color:var(--gray-700);
      border:none; border-radius:10px; font-size:13px; font-weight:700;
      cursor:pointer; font-family:inherit; transition:background 0.2s;
    }
    .btn-back:hover { background:var(--gray-200); }
    .btn-confirm-cancel {
      flex:1.5; padding:11px; background:var(--red); color:white;
      border:none; border-radius:10px; font-size:13px; font-weight:800;
      cursor:pointer; font-family:inherit; transition:opacity 0.2s;
      display:flex; align-items:center; justify-content:center; gap:7px;
    }
    .btn-confirm-cancel:hover { opacity:0.88; }
    .btn-confirm-cancel:disabled { opacity:0.5; cursor:not-allowed; }

    /* ═══ SPINNER ═══ */
    .spinner {
      width:15px; height:15px; border:2.5px solid rgba(255,255,255,0.4);
      border-top-color:#fff; border-radius:50%;
      animation:spin 0.6s linear infinite; display:inline-block;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* ═══ TOAST ═══ */
    .toast {
      position:fixed; bottom:20px; right:20px;
      background:var(--green); color:#fff;
      padding:12px 18px; border-radius:10px;
      font-size:13px; font-weight:700;
      box-shadow:0 4px 20px rgba(0,0,0,0.2);
      display:flex; align-items:center; gap:8px;
      z-index:9999; opacity:0; transform:translateY(10px);
      transition:all 0.3s; pointer-events:none;
    }
    .toast.show { opacity:1; transform:translateY(0); }
    .toast.error { background:var(--red); }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width:768px) {
      .nav-links { display:none; }
      .hamburger { display:block; }
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
    <a href="{{ route('customer.orders') }}" class="active"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
    <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
  </div>
  <div class="nav-right">
    <button class="notif-btn" title="Notifications">
      <i class="fa-solid fa-bell"></i>
      @if($unreadNotifications > 0)
        <span class="notif-badge show">{{ $unreadNotifications }}</span>
      @endif
    </button>
    <a href="{{ route('customer.cart') }}" class="cart-btn" title="My Cart">
      <i class="fa-solid fa-cart-shopping"></i>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
    </form>
    <button class="hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>
</nav>
<div class="mobile-menu" id="mobileMenu">
  <a href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Shop</a>
  <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
  <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
  <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
</div>

<!-- PAGE HEADER -->
<div class="page-header">
  <h1><i class="fa-solid fa-clipboard-list"></i> My Orders</h1>
  <p>Track and manage your fish orders</p>
</div>

<!-- CONTENT -->
<div class="container">

  @php
    $allOrders = $pendingOrders->concat($confirmedOrders)->concat($completedOrders)->sortByDesc('created_at');
  @endphp

  <!-- FILTER TABS -->
  <div class="filter-tabs">
    <button class="tab-btn active" onclick="filterTab('all', this)">
      <i class="fa-solid fa-list"></i> All
      <span class="tab-count">{{ $allOrders->count() }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('pending', this)">
      <i class="fa-solid fa-clock"></i> Pending
      <span class="tab-count">{{ $pendingOrders->count() }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('confirmed', this)">
      <i class="fa-solid fa-thumbs-up"></i> Confirmed
      <span class="tab-count">{{ $confirmedOrders->count() }}</span>
    </button>
    <button class="tab-btn" onclick="filterTab('completed', this)">
      <i class="fa-solid fa-circle-check"></i> Completed
      <span class="tab-count">{{ $completedOrders->count() }}</span>
    </button>
  </div>

  <!-- ORDERS LIST -->
  @if($allOrders->isEmpty())
    <div class="empty-state">
      <div class="empty-icon"><i class="fa-solid fa-clipboard-list"></i></div>
      <h3>Wala pang orders</h3>
      <p>Mag-order na ng sariwang isda!</p>
      <a class="btn-shop" href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Go to Shop</a>
    </div>
  @else
    <div class="orders-list" id="ordersList">

      @foreach($allOrders as $order)
      <div class="order-card" data-status="{{ $order->status }}">

        {{-- CARD HEADER --}}
        <div class="order-card-header">
          <div class="order-header-left">
            <div>
              <div class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
              <div class="order-date">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
            </div>
          </div>
          <span class="status-badge {{ $order->status }}">
            @if($order->status === 'pending')
              <i class="fa-solid fa-clock"></i> Pending
            @elseif($order->status === 'confirmed')
              <i class="fa-solid fa-thumbs-up"></i> Confirmed
            @elseif($order->status === 'completed')
              <i class="fa-solid fa-circle-check"></i> Completed
            @elseif($order->status === 'cancelled')
              <i class="fa-solid fa-ban"></i> Cancelled
            @endif
          </span>
        </div>

        {{-- PICKUP INFO --}}
        @if($order->pickup_date && in_array($order->status, ['pending','confirmed']))
        <div class="pickup-info">
          <i class="fa-solid fa-calendar-check"></i>
          <span>Pickup: <strong>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</strong></span>
        </div>
        @endif

        {{-- CANCEL REASON --}}
        @if($order->status === 'cancelled' && $order->cancel_reason)
        <div class="cancel-reason-box">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div><strong>Reason:</strong> {{ $order->cancel_reason }}</div>
        </div>
        @endif

        {{-- CARD BODY --}}
        <div class="order-card-body">
          <div class="order-items-list">
            @foreach($order->items as $item)
            <div class="order-item-row">
              <div class="item-img">
                @if($item->product && $item->product->image)
                  <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}"/>
                @else
                  <i class="fa-solid fa-fish"></i>
                @endif
              </div>
              <div class="item-details">
                <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                <div class="item-meta">{{ $item->quantity }}kg × ₱{{ number_format($item->unit_price, 2) }}/kg</div>
              </div>
              <div class="item-subtotal">₱{{ number_format($item->subtotal, 2) }}</div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- CARD FOOTER --}}
        <div class="order-card-footer">
          <div>
            <div class="order-total-label">Order Total</div>
            <div class="order-total-amount">₱{{ number_format($order->total_amount, 2) }}</div>
          </div>
          <div class="order-actions">
            @if(in_array($order->status, ['pending', 'confirmed']))
              <button
                class="btn-cancel-order"
                onclick="openCancelModal({{ $order->id }}, '#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}', '{{ number_format($order->total_amount, 2) }}')">
                <i class="fa-solid fa-ban"></i> Cancel Order
              </button>
            @endif
          </div>
        </div>

      </div>
      @endforeach

    </div>
  @endif

</div>

<!-- CANCEL MODAL -->
<div class="modal-overlay" id="cancelModal">
  <div class="cancel-modal">
    <div class="modal-handle"></div>
    <div class="modal-header-cancel">
      <div class="modal-cancel-icon"><i class="fa-solid fa-ban"></i></div>
      <div class="modal-cancel-title">Cancel Order?</div>
      <div class="modal-cancel-sub">Please tell us why you're cancelling.</div>
    </div>

    <div class="modal-order-summary" id="cancelSummary"></div>

    <div class="reason-section">
      <div class="reason-label"><i class="fa-solid fa-comment-dots"></i> Select a reason</div>
      <div class="reason-chips">
        <button class="reason-chip" onclick="selectChip(this, 'Changed my mind')">Changed my mind</button>
        <button class="reason-chip" onclick="selectChip(this, 'Ordered by mistake')">Ordered by mistake</button>
        <button class="reason-chip" onclick="selectChip(this, 'Found a better price')">Found better price</button>
        <button class="reason-chip" onclick="selectChip(this, 'Cannot pick up on time')">Cannot pick up</button>
        <button class="reason-chip" onclick="selectChip(this, 'Other')">Other</button>
      </div>
      <textarea class="reason-textarea" id="cancelReason"
        placeholder="Add more details (optional)..."
        oninput="updateCharCount(this)"></textarea>
      <div class="char-count"><span id="charCount">0</span>/200</div>
    </div>

    <div class="modal-footer-cancel">
      <button class="btn-back" onclick="closeCancelModal()">
        <i class="fa-solid fa-arrow-left"></i> Go Back
      </button>
      <button class="btn-confirm-cancel" id="confirmCancelBtn" onclick="submitCancel()">
        <i class="fa-solid fa-ban"></i> Confirm Cancel
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <i class="fa-solid fa-circle-check"></i>
  <span id="toastMsg"></span>
</div>

<script>
  const CSRF = '{{ csrf_token() }}';
  let cancelOrderId = null;
  let selectedReason = '';

  // ── Filter Tabs ──
  function filterTab(status, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.order-card').forEach(card => {
      card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });
  }

  // ── Cancel Modal ──
  function openCancelModal(orderId, orderNum, total) {
    cancelOrderId = orderId;
    selectedReason = '';
    document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));
    document.getElementById('cancelReason').value = '';
    document.getElementById('charCount').textContent = '0';
    document.getElementById('cancelSummary').innerHTML = `
      <div class="summary-row"><span class="summary-lbl">Order</span><span class="summary-val">${orderNum}</span></div>
      <div class="summary-row"><span class="summary-lbl">Amount</span><span class="summary-val" style="color:var(--red)">₱${total}</span></div>
    `;
    document.getElementById('cancelModal').classList.add('open');
  }

  function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('open');
    cancelOrderId = null;
  }

  function selectChip(el, reason) {
    document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedReason = reason;
    if (reason !== 'Other') {
      document.getElementById('cancelReason').value = reason;
      document.getElementById('charCount').textContent = reason.length;
    } else {
      document.getElementById('cancelReason').value = '';
      document.getElementById('cancelReason').focus();
    }
  }

  function updateCharCount(el) {
    const len = Math.min(el.value.length, 200);
    el.value = el.value.slice(0, 200);
    document.getElementById('charCount').textContent = len;
  }

  async function submitCancel() {
    const reason = document.getElementById('cancelReason').value.trim() || selectedReason;
    if (!reason) { showToast('Pakilagay ang dahilan ng cancellation.', true); return; }

    const btn = document.getElementById('confirmCancelBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Cancelling...';

    try {
      const res = await fetch(`/orders/${cancelOrderId}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ reason })
      });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error(data.message || 'Failed.');
      closeCancelModal();
      showToast('Order cancelled successfully.');
      setTimeout(() => location.reload(), 1000);
    } catch (err) {
      showToast('Error: ' + err.message, true);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-ban"></i> Confirm Cancel';
    }
  }

  // Close modal on backdrop click
  document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
  });

  // ── Toast ──
  function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast' + (isError ? ' error' : '') + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
  }
</script>

</body>
</html>