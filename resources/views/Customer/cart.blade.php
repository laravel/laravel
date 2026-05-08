{{-- resources/views/customer/cart.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda – My Cart</title>
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
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-900: #0f172a;
      --radius: 12px;
      --shadow: 0 4px 20px rgba(0,0,0,0.08);
      --shadow-lg: 0 8px 40px rgba(0,0,0,0.15);
    }

    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--gray-100); min-height:100vh; color:var(--gray-700); }

    /* ═══════════════════════════════════════ NAV */
    nav {
      background: linear-gradient(90deg,#2a7db5,#0d2b45);
      padding: 0 1.5rem;
      display: flex; align-items: center; justify-content: space-between;
      height: 62px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.2);
      position: sticky; top:0; z-index:100;
    }
    .nav-logo { color:#fff; font-size:20px; font-weight:800; text-decoration:none; display:flex; align-items:center; gap:8px; letter-spacing:-0.3px; }
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

    /* Notification Bell */
    .notif-btn {
      position:relative; color:#fff; font-size:18px;
      padding:8px; border-radius:8px;
      background:rgba(255,255,255,0.15);
      cursor:pointer; border:none;
      display:flex; align-items:center; justify-content:center;
      transition:background 0.2s;
    }
    .notif-btn:hover { background:rgba(255,255,255,0.25); }
    .notif-badge {
      position:absolute; top:-2px; right:-2px;
      background:#ef4444; color:#fff; font-size:9px; font-weight:800;
      min-width:16px; height:16px; border-radius:20px; padding:0 4px;
      display:none; align-items:center; justify-content:center;
      border:2px solid #1a5a8a;
    }
    .notif-badge.show { display:flex; }

    /* Cart icon */
    .cart-btn {
      position:relative; color:#fff; text-decoration:none;
      font-size:18px; padding:8px;
      border-radius:8px; background:rgba(255,255,255,0.2);
      display:flex; align-items:center; justify-content:center;
      transition:background 0.2s;
    }
    .cart-btn:hover { background:rgba(255,255,255,0.3); }
    .cart-badge {
      position:absolute; top:-2px; right:-2px;
      background:#ef4444; color:#fff; font-size:9px; font-weight:800;
      min-width:16px; height:16px; border-radius:20px; padding:0 4px;
      display:none; align-items:center; justify-content:center;
      border:2px solid #1a5a8a;
    }
    .btn-logout {
      background:rgba(255,255,255,0.12); color:#fff;
      border:1px solid rgba(255,255,255,0.25); border-radius:8px;
      padding:7px 13px; font-size:13px; cursor:pointer;
      display:flex; align-items:center; gap:6px; font-family:inherit;
      transition:background 0.2s;
    }
    .btn-logout:hover { background:rgba(255,255,255,0.22); }

    /* Hamburger */
    .hamburger { display:none; color:#fff; font-size:22px; background:none; border:none; cursor:pointer; padding:6px; }
    .mobile-menu {
      display:none; flex-direction:column;
      background:var(--navy); padding:0.75rem 1rem;
      gap:0.25rem; border-bottom:2px solid var(--blue);
    }
    .mobile-menu.open { display:flex; }
    .mobile-menu a {
      color:rgba(255,255,255,0.85); text-decoration:none;
      padding:10px 14px; border-radius:8px; font-size:14px; font-weight:500;
      display:flex; align-items:center; gap:8px;
    }
    .mobile-menu a:hover { background:rgba(255,255,255,0.1); color:#fff; }

    /* ═══════════════════════════════════════ PAGE HEADER */
    .page-header {
      background:linear-gradient(135deg,#2a7db5,#0d2b45);
      color:#fff; padding:2rem 1.5rem; text-align:center;
    }
    .page-header h1 { font-size:1.6rem; font-weight:800; display:flex; align-items:center; justify-content:center; gap:10px; }
    .page-header p { opacity:0.8; margin-top:4px; font-size:13px; }

    /* ═══════════════════════════════════════ CONTAINER */
    .container { max-width:1100px; margin:0 auto; padding:1.5rem 1rem; }

    /* ═══════════════════════════════════════ EMPTY STATE */
    .empty-state {
      background:#fff; border-radius:var(--radius); padding:3.5rem 2rem;
      text-align:center; box-shadow:var(--shadow);
    }
    .empty-state .empty-icon { font-size:4rem; color:var(--gray-300); margin-bottom:1rem; }
    .empty-state h3 { font-size:18px; font-weight:700; color:var(--navy); margin-bottom:6px; }
    .empty-state p { font-size:14px; color:var(--gray-400); margin-bottom:1.5rem; }
    .btn-shop {
      background:var(--blue); color:#fff; border:none; border-radius:9px;
      padding:11px 24px; font-size:14px; font-weight:700; cursor:pointer;
      text-decoration:none; display:inline-flex; align-items:center; gap:7px;
      font-family:inherit; transition:background 0.2s;
    }
    .btn-shop:hover { background:var(--blue-mid); }

    /* ═══════════════════════════════════════ SELECT ALL BAR */
    .select-bar {
      background:#fff; border-radius:var(--radius); padding:0.85rem 1.2rem;
      box-shadow:var(--shadow); margin-bottom:0.75rem;
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
      flex-wrap:wrap;
    }
    .select-bar-left { display:flex; align-items:center; gap:10px; }
    .check-all { width:18px; height:18px; accent-color:var(--blue); cursor:pointer; }
    .select-label { font-size:13px; font-weight:600; color:var(--gray-700); }
    .btn-delete-selected {
      background:var(--red-light); color:var(--red); border:none; border-radius:7px;
      padding:7px 14px; font-size:12px; font-weight:700; cursor:pointer;
      display:flex; align-items:center; gap:5px; font-family:inherit;
      transition:background 0.2s; display:none;
    }
    .btn-delete-selected:hover { background:#fecaca; }
    .btn-delete-selected.show { display:flex; }

    /* ═══════════════════════════════════════ CART ITEM CARDS (Shopee-style) */
    .cart-items { display:flex; flex-direction:column; gap:0.75rem; }

    .cart-item {
      background:#fff; border-radius:var(--radius);
      box-shadow:var(--shadow); overflow:hidden;
      border:2px solid transparent; transition:border-color 0.2s;
    }
    .cart-item.selected { border-color:var(--blue); }

    .cart-item-inner {
      display:grid;
      grid-template-columns: 44px 90px 1fr auto;
      gap:1rem; align-items:center;
      padding:1rem 1.2rem;
    }

    .item-checkbox { display:flex; align-items:center; justify-content:center; }
    .item-checkbox input { width:18px; height:18px; accent-color:var(--blue); cursor:pointer; }

    /* Product image */
    .item-img {
      width:90px; height:90px; border-radius:8px; object-fit:cover;
      background:var(--gray-100);
      border:1px solid var(--gray-200);
    }
    .item-img-placeholder {
      width:90px; height:90px; border-radius:8px;
      background:linear-gradient(135deg,var(--blue-light),var(--gray-100));
      display:flex; align-items:center; justify-content:center;
      font-size:2rem; color:var(--blue);
    }

    /* Item info */
    .item-info { min-width:0; }
    .item-name { font-size:15px; font-weight:700; color:var(--navy); margin-bottom:4px; }
    .item-price-per-kg { font-size:12px; color:var(--gray-400); margin-bottom:10px; }
    .item-subtotal { font-size:18px; font-weight:800; color:var(--red); }

    /* Qty controls */
    .item-right { display:flex; flex-direction:column; align-items:flex-end; gap:10px; }
    .qty-controls { display:flex; align-items:center; gap:0; border:1.5px solid var(--gray-200); border-radius:8px; overflow:hidden; }
    .qty-btn {
      width:34px; height:34px; border:none; background:#fff; cursor:pointer;
      font-size:16px; color:var(--blue); font-weight:700;
      display:flex; align-items:center; justify-content:center;
      transition:background 0.15s;
    }
    .qty-btn:hover { background:var(--blue-light); }
    .qty-btn:disabled { color:var(--gray-300); cursor:not-allowed; }
    .qty-input {
      width:48px; height:34px; border:none; border-left:1.5px solid var(--gray-200); border-right:1.5px solid var(--gray-200);
      text-align:center; font-size:14px; font-weight:700; color:var(--gray-900);
      font-family:inherit; outline:none;
    }
    .btn-remove {
      background:none; border:none; color:var(--gray-400);
      font-size:12px; cursor:pointer; display:flex; align-items:center; gap:4px;
      font-family:inherit; padding:4px 6px; border-radius:6px;
      transition:all 0.2s;
    }
    .btn-remove:hover { color:var(--red); background:var(--red-light); }

    /* Action buttons on item */
    .item-actions { display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.5rem; }
    .btn-order-now {
      background:var(--orange); color:#fff; border:none; border-radius:7px;
      padding:8px 16px; font-size:12px; font-weight:700; cursor:pointer;
      display:flex; align-items:center; gap:5px; font-family:inherit;
      transition:background 0.2s;
    }
    .btn-order-now:hover { background:#c2410c; }
    .btn-add-reserve {
      background:var(--blue-light); color:var(--blue); border:1.5px solid var(--blue);
      border-radius:7px; padding:7px 16px; font-size:12px; font-weight:700;
      cursor:pointer; display:flex; align-items:center; gap:5px; font-family:inherit;
      transition:all 0.2s;
    }
    .btn-add-reserve:hover { background:var(--blue); color:#fff; }

    /* ═══════════════════════════════════════ CHECKOUT BAR */
    .checkout-bar {
      background:#fff; border-radius:var(--radius);
      box-shadow:var(--shadow); padding:1rem 1.5rem;
      margin-top:0.75rem;
      display:flex; align-items:center; justify-content:space-between;
      flex-wrap:wrap; gap:1rem;
    }
    .checkout-total-label { font-size:13px; color:var(--gray-400); }
    .checkout-total-amount { font-size:22px; font-weight:800; color:var(--red); }
    .checkout-right { display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; }
    .btn-checkout {
      background:var(--red); color:#fff; border:none; border-radius:9px;
      padding:12px 28px; font-size:14px; font-weight:800; cursor:pointer;
      display:flex; align-items:center; gap:7px; font-family:inherit;
      transition:background 0.2s; min-width:160px; justify-content:center;
    }
    .btn-checkout:hover { background:#b91c1c; }
    .btn-checkout:disabled { background:var(--gray-300); cursor:not-allowed; }
    .selected-items-count { font-size:13px; color:var(--gray-600); }

    /* ═══════════════════════════════════════ MODAL BASE */
    .modal-overlay {
      display:none; position:fixed; inset:0;
      background:rgba(0,0,0,0.55); z-index:1000;
      align-items:center; justify-content:center; padding:1rem;
    }
    .modal-overlay.open { display:flex; }

    .modal {
      background:#fff; border-radius:16px;
      box-shadow:var(--shadow-lg);
      width:100%; max-width:580px;
      max-height:90vh; overflow-y:auto;
      animation:modalIn 0.25s ease;
    }
    @keyframes modalIn { from { opacity:0; transform:scale(0.95) translateY(10px); } to { opacity:1; transform:none; } }

    .modal-header {
      padding:1.25rem 1.5rem;
      border-bottom:1px solid var(--gray-200);
      display:flex; align-items:center; justify-content:space-between;
      position:sticky; top:0; background:#fff; z-index:1;
      border-radius:16px 16px 0 0;
    }
    .modal-title { font-size:16px; font-weight:800; color:var(--navy); display:flex; align-items:center; gap:8px; }
    .modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:var(--gray-400); padding:4px; border-radius:6px; transition:color 0.2s; }
    .modal-close:hover { color:var(--red); }
    .modal-body { padding:1.5rem; }
    .modal-footer { padding:1rem 1.5rem; border-top:1px solid var(--gray-200); display:flex; gap:0.75rem; justify-content:flex-end; flex-wrap:wrap; }

    /* ═══════════════════════════════════════ ORDER MODAL - Item Summary */
    .order-items-list { display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.25rem; }
    .order-item-row {
      display:flex; align-items:center; gap:12px;
      background:var(--gray-50); border-radius:10px; padding:10px 12px;
      border:1px solid var(--gray-200);
    }
    .order-item-img {
      width:56px; height:56px; border-radius:7px; object-fit:cover;
      background:var(--blue-light); display:flex; align-items:center; justify-content:center;
      font-size:1.6rem; color:var(--blue); flex-shrink:0;
      border:1px solid var(--gray-200);
    }
    .order-item-img img { width:100%; height:100%; object-fit:cover; border-radius:7px; }
    .order-item-details { flex:1; min-width:0; }
    .order-item-name { font-size:14px; font-weight:700; color:var(--navy); }
    .order-item-meta { font-size:12px; color:var(--gray-400); margin-top:2px; }
    .order-item-price { font-size:15px; font-weight:800; color:var(--red); white-space:nowrap; }

    /* Section label */
    .form-section-label {
      font-size:11px; font-weight:700; color:var(--blue); text-transform:uppercase;
      letter-spacing:0.07em; margin-bottom:0.6rem; margin-top:1.1rem;
      display:flex; align-items:center; gap:6px;
    }
    .form-section-label:first-child { margin-top:0; }

    /* Form fields */
    .form-group { margin-bottom:0.85rem; }
    .form-label { font-size:12px; font-weight:600; color:var(--gray-600); margin-bottom:4px; display:block; }
    .form-input {
      width:100%; padding:9px 12px;
      border:1.5px solid var(--gray-200); border-radius:8px;
      font-size:13px; font-family:inherit; color:var(--gray-900);
      background:#fff; outline:none; transition:border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(42,125,181,0.1); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; }

    /* Order total in modal */
    .modal-total-row {
      background:var(--orange-light); border-radius:10px; padding:12px 16px;
      display:flex; align-items:center; justify-content:space-between;
      border:1.5px solid #fed7aa; margin-bottom:1rem;
    }
    .modal-total-label { font-size:13px; font-weight:600; color:var(--orange); }
    .modal-total-amount { font-size:20px; font-weight:800; color:var(--orange); }

    /* Note box */
    .note-box {
      background:#fffbeb; border:1.5px solid #fbbf24;
      border-radius:10px; padding:12px 14px;
      font-size:12px; color:#92400e;
      display:flex; gap:8px; align-items:flex-start;
      margin-bottom:1rem;
    }
    .note-box i { color:#f59e0b; margin-top:1px; flex-shrink:0; }

    /* Modal action buttons */
    .btn-modal-cancel {
      background:var(--gray-100); color:var(--gray-700); border:none;
      border-radius:9px; padding:10px 20px; font-size:13px; font-weight:700;
      cursor:pointer; font-family:inherit; transition:background 0.2s;
    }
    .btn-modal-cancel:hover { background:var(--gray-200); }
    .btn-modal-place {
      background:linear-gradient(135deg,#ea580c,#dc2626); color:#fff; border:none;
      border-radius:9px; padding:10px 24px; font-size:14px; font-weight:800;
      cursor:pointer; font-family:inherit; transition:opacity 0.2s;
      display:flex; align-items:center; gap:7px;
    }
    .btn-modal-place:hover { opacity:0.9; }

    /* ═══════════════════════════════════════ SUCCESS TOAST */
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

    /* ═══════════════════════════════════════ RESPONSIVE */
    @media (max-width: 768px) {
      .nav-links { display:none; }
      .hamburger { display:block; }
      .cart-item-inner { grid-template-columns:34px 70px 1fr; }
      .item-right { display:none; }
      .item-img, .item-img-placeholder { width:70px; height:70px; }
      .form-row { grid-template-columns:1fr; }
    }
    @media (max-width: 480px) {
      .cart-item-inner { grid-template-columns:30px 60px 1fr; gap:0.6rem; padding:0.75rem; }
      .item-img, .item-img-placeholder { width:60px; height:60px; font-size:1.5rem; }
      .checkout-bar { flex-direction:column; align-items:stretch; }
      .btn-checkout { width:100%; }
    }
  </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════ NAV -->
<nav>
  <a class="nav-logo" href="#"><i class="fa-solid fa-fish"></i> eIsda</a>
  <div class="nav-links">
    <a href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Shop</a>
    <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
    <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
    <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
  </div>
  <div class="nav-right">
    <!-- Notification Bell -->
    <button class="notif-btn" onclick="toggleNotifications()" title="Notifications">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-badge" id="notifBadge"></span>
    </button>
    <!-- Cart -->
    <a href="{{ route('customer.cart') }}" class="cart-btn active" title="My Cart">
      <i class="fa-solid fa-cart-shopping"></i>
      <span class="cart-badge" id="cartBadge"></span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
    </form>
    <button class="hamburger" onclick="toggleMobile()"><i class="fa-solid fa-bars"></i></button>
  </div>
</nav>
<div class="mobile-menu" id="mobileMenu">
  <a href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Shop</a>
  <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
  <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
  <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
</div>

<!-- ═══════════════════════════════════════════════════════ PAGE HEADER -->
<div class="page-header">
  <h1><i class="fa-solid fa-cart-shopping"></i> My Cart</h1>
  <p>Review your items and place your order</p>
</div>

<!-- ═══════════════════════════════════════════════════════ MAIN CONTENT -->
<div class="container">

  <!-- Empty State -->
  <div class="empty-state" id="emptyState" style="display:none;">
    <div class="empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
    <h3>Your cart is empty</h3>
    <p>Add some fresh seafood from the shop!</p>
    <a class="btn-shop" href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Go to Shop</a>
  </div>

  <!-- Select All Bar -->
  <div class="select-bar" id="selectBar" style="display:none;">
    <div class="select-bar-left">
      <input type="checkbox" class="check-all" id="selectAll" onchange="toggleSelectAll(this)"/>
      <span class="select-label">Select All (<span id="totalItemsLabel">0</span> items)</span>
    </div>
    <button class="btn-delete-selected" id="btnDeleteSelected" onclick="removeSelected()">
      <i class="fa-solid fa-trash"></i> Delete
    </button>
  </div>

  <!-- Cart Items -->
  <div class="cart-items" id="cartItems"></div>

  <!-- Checkout Bar -->
  <div class="checkout-bar" id="checkoutBar" style="display:none;">
    <div>
      <div class="checkout-total-label">Total (<span id="selectedCount">0</span> item(s))</div>
      <div class="checkout-total-amount" id="checkoutTotal">₱0</div>
    </div>
    <div class="checkout-right">
      <button class="btn-checkout" id="btnCheckout" onclick="openOrderModal(false)" disabled>
        <i class="fa-solid fa-paper-plane"></i> Place Order
      </button>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════ ORDER MODAL -->
<div class="modal-overlay" id="orderModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-bag-shopping"></i> Confirm Order</div>
      <button class="modal-close" onclick="closeModal('orderModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">

      <!-- Items summary -->
      <div class="form-section-label"><i class="fa-solid fa-fish"></i> Items to Order</div>
      <div class="order-items-list" id="modalItemsList"></div>

      <!-- Total -->
      <div class="modal-total-row">
        <div class="modal-total-label"><i class="fa-solid fa-receipt"></i> Order Total</div>
        <div class="modal-total-amount" id="modalTotal">₱0</div>
      </div>

      <!-- Customer Info -->
      <div class="form-section-label"><i class="fa-solid fa-user"></i> Delivery Information</div>
      <p style="font-size:12px;color:var(--gray-400);margin-bottom:10px;">Review and edit your info below if needed.</p>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" class="form-input" id="orderName" placeholder="Your full name" value="{{ auth()->user()->name ?? '' }}"/>
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="tel" class="form-input" id="orderPhone" placeholder="09xxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}"/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Delivery Address *</label>
        <input type="text" class="form-input" id="orderAddress" placeholder="Street, Barangay, City" value="{{ auth()->user()->address ?? '' }}"/>
      </div>
      <div class="form-group">
        <label class="form-label">Additional Notes (optional)</label>
        <input type="text" class="form-input" id="orderNotes" placeholder="e.g. Leave at the gate"/>
      </div>

      <!-- Important Note -->
      <div class="note-box">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
          <strong>Important:</strong> Please pick up your order on time. Unclaimed orders past the scheduled time will be made available for other customers. eIsda reserves the right to release unclaimed reservations.
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button class="btn-modal-cancel" onclick="closeModal('orderModal')">Cancel</button>
      <button class="btn-modal-place" onclick="submitOrder()">
        <i class="fa-solid fa-paper-plane"></i> Place Order
      </button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ SINGLE ORDER MODAL -->
{{-- Used when "Order Now" is clicked on a single item --}}
<div class="modal-overlay" id="singleOrderModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-bolt"></i> Order Now</div>
      <button class="modal-close" onclick="closeModal('singleOrderModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="order-items-list" id="singleModalItemsList"></div>
      <div class="modal-total-row">
        <div class="modal-total-label"><i class="fa-solid fa-receipt"></i> Total</div>
        <div class="modal-total-amount" id="singleModalTotal">₱0</div>
      </div>
      <div class="form-section-label"><i class="fa-solid fa-user"></i> Delivery Information</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input type="text" class="form-input" id="singleOrderName" placeholder="Your full name" value="{{ auth()->user()->name ?? '' }}"/>
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number *</label>
          <input type="tel" class="form-input" id="singleOrderPhone" placeholder="09xxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}"/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Delivery Address *</label>
        <input type="text" class="form-input" id="singleOrderAddress" placeholder="Street, Barangay, City" value="{{ auth()->user()->address ?? '' }}"/>
      </div>
      <div class="form-group">
        <label class="form-label">Additional Notes (optional)</label>
        <input type="text" class="form-input" id="singleOrderNotes" placeholder="e.g. Leave at the gate"/>
      </div>
      <div class="note-box">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
          <strong>Important:</strong> Please pick up your order on time. Unclaimed orders past the scheduled time will be made available for other customers. eIsda reserves the right to release unclaimed reservations.
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-cancel" onclick="closeModal('singleOrderModal')">Cancel</button>
      <button class="btn-modal-place" onclick="submitSingleOrder()">
        <i class="fa-solid fa-paper-plane"></i> Place Order
      </button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ TOAST -->
<div class="toast" id="toast">
  <i class="fa-solid fa-circle-check"></i>
  <span id="toastMsg">Order placed!</span>
</div>

<script>
  // ── Product data (keep in sync with shop page)
  const PRICES = {
    'Tilapia':160,'Bangus':210,'Galunggong':120,
    'Lapu-Lapu':260,'Hipon':320,'Pusit':280
  };
  const ICONS = {
    'Tilapia':'🐟','Bangus':'🐠','Galunggong':'🐡',
    'Lapu-Lapu':'🐡','Hipon':'🦐','Pusit':'🦑'
  };
  // Images map — replace with actual URLs or route to your images
  const IMAGES = {
    'Tilapia': null,
    'Bangus': null,
    'Galunggong': null,
    'Lapu-Lapu': null,
    'Hipon': null,
    'Pusit': null
  };

  let singleOrderItemId = null; // track which item for single order

  /* ─── Storage ─── */
  function getCart()   { return JSON.parse(localStorage.getItem('eisda_cart') || '[]'); }
  function saveCart(c) { localStorage.setItem('eisda_cart', JSON.stringify(c)); updateBadge(); }

  function updateBadge() {
    const cart = getCart();
    const b = document.getElementById('cartBadge');
    if (cart.length > 0) { b.textContent = cart.length; b.style.display = 'flex'; }
    else b.style.display = 'none';
  }

  /* ─── Render ─── */
  function renderCart() {
    const cart = getCart();
    const emptyState  = document.getElementById('emptyState');
    const selectBar   = document.getElementById('selectBar');
    const cartItemsEl = document.getElementById('cartItems');
    const checkoutBar = document.getElementById('checkoutBar');
    const totalLabel  = document.getElementById('totalItemsLabel');

    updateBadge();
    cartItemsEl.innerHTML = '';

    if (cart.length === 0) {
      emptyState.style.display  = 'block';
      selectBar.style.display   = 'none';
      checkoutBar.style.display = 'none';
      return;
    }

    emptyState.style.display  = 'none';
    selectBar.style.display   = 'flex';
    checkoutBar.style.display = 'flex';
    totalLabel.textContent     = cart.length;

    cart.forEach(item => {
      const subtotal = item.price * item.qty;
      const icon     = ICONS[item.product] || '🐟';
      const imgSrc = item.image || null;

      const div = document.createElement('div');
      div.className = 'cart-item';
      div.id = 'item-' + item.id;
      div.innerHTML = `
        <div class="cart-item-inner">
          <div class="item-checkbox">
            <input type="checkbox" class="item-check" data-id="${item.id}" onchange="onCheckChange()"/>
          </div>
          <div class="item-img-placeholder" id="img-${item.id}">
            ${imgSrc
              ? `<img src="${imgSrc}" alt="${item.product}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;"/>`
              : icon}
          </div>
          <div class="item-info">
            <div class="item-name">${item.product}</div>
            <div class="item-price-per-kg">₱${item.price.toLocaleString()} / kg</div>
            <div class="item-subtotal" id="subtotal-${item.id}">₱${subtotal.toLocaleString()}</div>
            <div class="item-actions">
              <button class="btn-order-now" onclick="openSingleOrderModal(${item.id})">
                <i class="fa-solid fa-bolt"></i> Order Now
              </button>
            </div>
          </div>
          <div class="item-right">
            <div class="qty-controls">
              <button class="qty-btn" onclick="changeQty(${item.id}, -1)" ${item.qty <= 1 ? 'disabled' : ''}>−</button>
              <input type="number" class="qty-input" value="${item.qty}" min="1"
                     onchange="setQty(${item.id}, this.value)" onblur="setQty(${item.id}, this.value)"/>
              <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
            </div>
            <button class="btn-remove" onclick="removeItem(${item.id})">
              <i class="fa-solid fa-trash"></i> Remove
            </button>
          </div>
        </div>
      `;
      cartItemsEl.appendChild(div);
    });

    // Restore checkbox states & update totals
    onCheckChange();
  }

  /* ─── Qty ─── */
  function changeQty(id, delta) {
    let cart = getCart();
    const idx = cart.findIndex(i => i.id === id);
    if (idx === -1) return;
    cart[idx].qty = Math.max(1, cart[idx].qty + delta);
    saveCart(cart);
    renderCart();
  }
  function setQty(id, val) {
    let cart = getCart();
    const idx = cart.findIndex(i => i.id === id);
    if (idx === -1) return;
    cart[idx].qty = Math.max(1, parseInt(val) || 1);
    saveCart(cart);
    renderCart();
  }

  /* ─── Remove ─── */
  function removeItem(id) {
    if (!confirm('Remove this item from your cart?')) return;
    let cart = getCart().filter(i => i.id !== id);
    saveCart(cart);
    renderCart();
  }
  function removeSelected() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    if (!confirm(`Remove ${ids.length} selected item(s)?`)) return;
    let cart = getCart().filter(i => !ids.includes(i.id));
    saveCart(cart);
    renderCart();
  }

  /* ─── Checkbox ─── */
  function getSelectedIds() {
    return [...document.querySelectorAll('.item-check:checked')].map(c => parseInt(c.dataset.id));
  }
  function onCheckChange() {
    const ids   = getSelectedIds();
    const total = document.querySelectorAll('.item-check').length;
    const sa    = document.getElementById('selectAll');
    const delBtn = document.getElementById('btnDeleteSelected');
    const btnCheckout = document.getElementById('btnCheckout');
    const selectedCount = document.getElementById('selectedCount');

    sa.checked       = ids.length === total && total > 0;
    sa.indeterminate = ids.length > 0 && ids.length < total;

    delBtn.classList.toggle('show', ids.length > 0);
    btnCheckout.disabled = ids.length === 0;

    // Highlight
    document.querySelectorAll('.item-check').forEach(cb => {
      document.getElementById('item-' + cb.dataset.id)
        .classList.toggle('selected', cb.checked);
    });

    // Update checkout total
    const cart = getCart();
    let total_amount = 0;
    ids.forEach(id => {
      const item = cart.find(i => i.id === id);
      if (item) total_amount += item.price * item.qty;
    });
    document.getElementById('checkoutTotal').textContent = '₱' + total_amount.toLocaleString();
    selectedCount.textContent = ids.length;
  }
  function toggleSelectAll(cb) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = cb.checked);
    onCheckChange();
  }

  /* ─── Order Modal (bulk/selected) ─── */
  function openOrderModal() {
    const ids  = getSelectedIds();
    const cart = getCart();
    if (!ids.length) return;

    const items = ids.map(id => cart.find(i => i.id === id)).filter(Boolean);
    let total = 0;
    const listEl = document.getElementById('modalItemsList');
    listEl.innerHTML = '';

    items.forEach(item => {
      const subtotal = item.price * item.qty;
      total += subtotal;
      const icon = ICONS[item.product] || '🐟';
      const imgSrc = IMAGES[item.product];

      const row = document.createElement('div');
      row.className = 'order-item-row';
      row.innerHTML = `
        <div class="order-item-img">
          ${imgSrc ? `<img src="${imgSrc}" alt="${item.product}"/>` : icon}
        </div>
        <div class="order-item-details">
          <div class="order-item-name">${item.product}</div>
          <div class="order-item-meta">${item.qty} kg × ₱${item.price.toLocaleString()}/kg</div>
        </div>
        <div class="order-item-price">₱${subtotal.toLocaleString()}</div>
      `;
      listEl.appendChild(row);
    });

    document.getElementById('modalTotal').textContent = '₱' + total.toLocaleString();
    document.getElementById('orderModal').classList.add('open');
  }

  /* ─── Single Order Modal ─── */
  function openSingleOrderModal(id) {
    const cart = getCart();
    const item = cart.find(i => i.id === id);
    if (!item) return;
    singleOrderItemId = id;

    const icon   = ICONS[item.product] || '🐟';
    const imgSrc = IMAGES[item.product];
    const subtotal = item.price * item.qty;

    const listEl = document.getElementById('singleModalItemsList');
    listEl.innerHTML = `
      <div class="order-item-row">
        <div class="order-item-img">
          ${imgSrc ? `<img src="${imgSrc}" alt="${item.product}"/>` : icon}
        </div>
        <div class="order-item-details">
          <div class="order-item-name">${item.product}</div>
          <div class="order-item-meta">${item.qty} kg × ₱${item.price.toLocaleString()}/kg</div>
        </div>
        <div class="order-item-price">₱${subtotal.toLocaleString()}</div>
      </div>
    `;
    document.getElementById('singleModalTotal').textContent = '₱' + subtotal.toLocaleString();
    document.getElementById('singleOrderModal').classList.add('open');
  }

  /* ─── Submit Order (selected items) ─── */
  function submitOrder() {
    const ids    = getSelectedIds();
    const cart   = getCart();
    const name   = document.getElementById('orderName').value.trim();
    const phone  = document.getElementById('orderPhone').value.trim();
    const addr   = document.getElementById('orderAddress').value.trim();
    const notes  = document.getElementById('orderNotes').value.trim();

    if (!name || !phone || !addr) {
      alert('Please fill in all required fields.');
      return;
    }

    const items = ids.map(id => {
  const item = cart.find(i => i.id === id);
  return { product_id: item.product_id, quantity: item.qty };
}).filter(Boolean);

    fetch('{{ route("customer.orders.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ name, phone, address: addr, notes, items })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Remove ordered items from cart
        let newCart = cart.filter(i => !ids.includes(i.id));
        saveCart(newCart);
        closeModal('orderModal');
        renderCart();
        showToast('🎉 Order placed successfully!');
        setTimeout(() => location.reload(), 2000);
      } else {
        alert(data.message || 'Something went wrong. Please try again.');
      }
    })
    .catch(() => alert('Network error. Please try again.'));
  }

  /* ─── Submit Single Order ─── */
  function submitSingleOrder() {
    const cart   = getCart();
    const item   = cart.find(i => i.id === singleOrderItemId);
    if (!item) return;

    const name   = document.getElementById('singleOrderName').value.trim();
    const phone  = document.getElementById('singleOrderPhone').value.trim();
    const addr   = document.getElementById('singleOrderAddress').value.trim();
    const notes  = document.getElementById('singleOrderNotes').value.trim();

    if (!name || !phone || !addr) {
      alert('Please fill in all required fields.');
      return;
    }

    fetch('{{ route("customer.orders.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        name, phone, address: addr, notes,
        items: [{ product: item.product, qty: item.qty, price: item.price }]
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        let newCart = cart.filter(i => i.id !== singleOrderItemId);
        saveCart(newCart);
        closeModal('singleOrderModal');
        renderCart();
        showToast('🎉 Order placed successfully!');
      } else {
        alert(data.message || 'Something went wrong. Please try again.');
      }
    })
    .catch(() => alert('Network error. Please try again.'));
  }

  /* ─── Modal helpers ─── */
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
  }
  // Close on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) overlay.classList.remove('open');
    });
  });

  /* ─── Toast ─── */
  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  /* ─── Mobile nav ─── */
  function toggleMobile() {
    document.getElementById('mobileMenu').classList.toggle('open');
  }

  /* ─── Notifications (placeholder) ─── */
  function toggleNotifications() {
    // Will be wired to actual notification system
    // For now just show a placeholder
    showToast('Notifications coming soon!');
  }

  // Set notif badge from server data if any
  const unreadCount = <?php echo isset($unreadNotifications) ? $unreadNotifications : 0; ?>;
if (unreadCount > 0) {
    document.getElementById('notifBadge').textContent = unreadCount;
    document.getElementById('notifBadge').classList.add('show');
}

  /* ─── Init ─── */
  renderCart();
</script>

</body>
</html>