<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda - Shop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --blue: #2a7db5;
      --blue-dark: #1a5a8a;
      --blue-deeper: #0e3d5c;
      --green: #27ae60;
      --red: #e74c3c;
      --bg: #f0f4f8;
      --white: #ffffff;
      --text: #1a3a52;
      --muted: #6b7f8e;
      --border: #d0dce8;
      --card-shadow: 0 2px 16px rgba(0,0,0,0.08);
    }

    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); min-height: 100vh; }

    /* ===== NAV ===== */
    nav {
      background: linear-gradient(90deg, var(--blue), var(--blue-dark));
      padding: 0 1.5rem; display: flex; align-items: center;
      justify-content: space-between; height: 60px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.18);
      position: sticky; top: 0; z-index: 100;
    }
    .nav-logo { color: white; font-size: 19px; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 8px; }
    .nav-links { display: flex; gap: 0.25rem; }
    .nav-links a {
      color: rgba(255,255,255,0.85); text-decoration: none; padding: 7px 13px;
      border-radius: 8px; font-size: 13px; font-weight: 500;
      transition: background 0.2s; display: flex; align-items: center; gap: 5px;
    }
    .nav-links a:hover { background: rgba(255,255,255,0.15); color: white; }
    .nav-links a.active { background: rgba(255,255,255,0.22); color: white; font-weight: 700; }
    .nav-right { display: flex; align-items: center; gap: 0.5rem; }

    .icon-btn {
      position: relative; color: white; text-decoration: none;
      font-size: 18px; display: flex; align-items: center;
      padding: 7px 9px; border-radius: 8px; transition: background 0.2s;
      background: none; border: none; cursor: pointer;
    }
    .icon-btn:hover { background: rgba(255,255,255,0.15); }

    .badge {
      position: absolute; top: 2px; right: 2px;
      background: var(--red); color: white;
      font-size: 9px; font-weight: 800;
      min-width: 16px; height: 16px;
      border-radius: 50%; display: none;
      align-items: center; justify-content: center; padding: 0 3px;
    }

    .btn-logout {
      background: rgba(255,255,255,0.13); color: white;
      border: 1px solid rgba(255,255,255,0.28); border-radius: 8px;
      padding: 6px 13px; font-size: 12px; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; gap: 6px;
    }
    .btn-logout:hover { background: rgba(255,255,255,0.22); }

    .hamburger { display: none; background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 6px; }

    /* ===== HERO ===== */
    .hero {
      background: linear-gradient(135deg, var(--blue) 0%, var(--blue-deeper) 100%);
      color: white; text-align: center; padding: 4rem 1.5rem 5rem;
      position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-content { position: relative; z-index: 1; }
    .hero h1 { font-size: clamp(1.6rem, 4vw, 2.4rem); font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 12px; }
    .hero p { font-size: 1rem; opacity: 0.85; margin-bottom: 1.75rem; }
    .btn-hero {
      background: white; color: var(--blue); border: none; border-radius: 10px;
      padding: 13px 30px; font-size: 15px; font-weight: 700;
      cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
      text-decoration: none; transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .btn-hero:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.25); }

    /* ===== PRODUCTS ===== */
    .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }
    .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem; }
    .section-title { font-size: 1.15rem; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .product-count { font-size: 12px; background: var(--blue); color: white; border-radius: 20px; padding: 2px 10px; font-weight: 700; }

    .product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }

    .product-card {
      background: white; border-radius: 16px; overflow: hidden;
      box-shadow: var(--card-shadow); transition: transform 0.22s, box-shadow 0.22s;
    }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 32px rgba(0,0,0,0.14); }

    .product-img {
      width: 100%; height: 160px;
      background: linear-gradient(135deg, #ddeef8, #c5e0f5);
      display: flex; align-items: center; justify-content: center;
      font-size: 4rem; color: var(--blue); position: relative; overflow: hidden;
    }
    .product-img img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
    .product-img .img-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 55%, rgba(14,61,92,0.12)); }
    .stock-badge { position: absolute; top: 10px; right: 10px; background: var(--green); color: white; font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
    .out-of-stock-badge { position: absolute; top: 10px; right: 10px; background: var(--red); color: white; font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }

    .product-info { padding: 1rem; }
    .product-name { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 3px; }
    .product-price { color: var(--blue); font-weight: 800; font-size: 16px; display: flex; align-items: baseline; gap: 4px; margin-bottom: 6px; }
    .product-unit { color: var(--muted); font-size: 11px; font-weight: 500; }
    .stock-remaining { font-size: 11px; font-weight: 600; color: var(--green); display: flex; align-items: center; gap: 4px; margin-bottom: 10px; }
    .stock-remaining.low { color: #e67e22; }
    .stock-remaining.out { color: var(--red); }

    .btn-group { display: flex; gap: 6px; }
    .btn-add {
      flex: 1; padding: 9px 4px; background: var(--blue); color: white;
      border: none; border-radius: 9px; font-size: 12px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;
      transition: background 0.2s;
    }
    .btn-add:hover { background: var(--blue-dark); }
    .btn-add:disabled { background: #b0c4d8; cursor: not-allowed; }
    .btn-order-card {
      flex: 1; padding: 9px 4px; background: white; color: var(--blue);
      border: 1.5px solid var(--blue); border-radius: 9px; font-size: 12px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;
      transition: background 0.2s;
    }
    .btn-order-card:hover { background: #eef6fc; }
    .btn-order-card:disabled { border-color: #b0c4d8; color: #b0c4d8; cursor: not-allowed; }

    /* ===== MODAL BASE ===== */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 1000;
      align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-card {
      background: white; border-radius: 18px; width: 100%; max-width: 440px;
      box-shadow: 0 16px 60px rgba(0,0,0,0.25); position: relative;
      animation: slideUp 0.22s ease; max-height: 92vh; overflow-y: auto;
    }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-close {
      position: absolute; top: 14px; right: 16px;
      background: rgba(0,0,0,0.15); border: none; border-radius: 50%;
      width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
      font-size: 14px; color: white; cursor: pointer; z-index: 2;
    }
    .modal-close:hover { background: rgba(0,0,0,0.3); }

    .modal-img-area {
      width: 100%; height: 180px;
      background: linear-gradient(135deg, #ddeef8, #b8d9f0);
      display: flex; align-items: center; justify-content: center;
      font-size: 5rem; color: var(--blue);
      border-radius: 18px 18px 0 0; position: relative; overflow: hidden;
    }
    .modal-img-area img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
    .modal-prod-badge {
      position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
      background: var(--blue); color: white; font-size: 12px; font-weight: 700;
      padding: 4px 14px; border-radius: 20px; white-space: nowrap; z-index: 1;
    }
    .modal-body { padding: 1.4rem; }
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 1rem; display: flex; align-items: center; gap: 7px; }

    .modal-stock-info {
      display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
      background: #f0fff4; border: 1px solid #b7ebc8;
      border-radius: 8px; padding: 7px 12px; margin-bottom: 1rem; color: var(--green);
    }
    .modal-stock-info.low { background: #fff8f0; border-color: #fdd5a0; color: #e67e22; }
    .modal-stock-info.out { background: #fff0f0; border-color: #fdb5b5; color: var(--red); }

    .price-display {
      background: #f0f7ff; border-radius: 10px; padding: 12px 14px;
      margin-bottom: 1.1rem; display: flex; justify-content: space-between; align-items: center;
    }
    .price-display .label { font-size: 12px; color: var(--muted); font-weight: 600; }
    .price-display .total { font-size: 1.4rem; font-weight: 800; color: var(--blue); }
    .price-display .per-unit { font-size: 12px; color: var(--muted); text-align: right; }

    .qty-control {
      display: flex; align-items: center; gap: 12px;
      background: #f8fafc; border: 1.5px solid var(--border);
      border-radius: 10px; padding: 8px 14px; margin-bottom: 1rem;
    }
    .qty-control label { font-size: 12px; font-weight: 700; color: var(--muted); flex: 1; }
    .qty-btns { display: flex; align-items: center; gap: 10px; }
    .qty-btn {
      width: 30px; height: 30px; border-radius: 50%;
      border: 2px solid var(--blue); background: white; color: var(--blue);
      font-size: 16px; font-weight: 700; cursor: pointer;
      display: flex; align-items: center; justify-content: center; transition: all 0.15s;
    }
    .qty-btn:hover { background: var(--blue); color: white; }
    .qty-num { font-size: 18px; font-weight: 800; color: var(--text); min-width: 30px; text-align: center; }

    .modal-field { margin-bottom: 0.9rem; }
    .modal-field label {
      display: flex; align-items: center; gap: 5px;
      font-size: 11px; font-weight: 700; color: var(--muted);
      margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .modal-field input {
      width: 100%; padding: 10px 12px;
      border: 1.5px solid var(--border); border-radius: 9px;
      font-size: 14px; font-family: inherit; color: var(--text); outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .modal-field input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(42,125,181,0.1); }

    .note-box {
      background: #fffbeb; border: 1.5px solid #fbbf24;
      border-radius: 10px; padding: 12px 14px;
      font-size: 12px; color: #92400e;
      display: flex; gap: 8px; align-items: flex-start; margin-bottom: 1rem; line-height: 1.5;
    }
    .note-box i { color: #f59e0b; margin-top: 2px; flex-shrink: 0; }

    .btn-confirm {
      width: 100%; padding: 13px; background: var(--blue); color: white;
      border: none; border-radius: 10px; font-size: 14px; font-weight: 800;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-confirm:hover:not(:disabled) { background: var(--blue-dark); transform: translateY(-1px); }
    .btn-confirm:disabled { background: #b0c4d8; cursor: not-allowed; transform: none; }
    .btn-confirm.green { background: var(--green); }
    .btn-confirm.green:hover:not(:disabled) { background: #219150; }

    /* ===== ATC SUCCESS ===== */
    .modal-success { display: none; text-align: center; padding: 2.5rem 1.5rem; }
    .success-icon { font-size: 4rem; color: var(--green); margin-bottom: 1rem; animation: pop 0.3s ease; }
    @keyframes pop { 0%{transform:scale(0)} 70%{transform:scale(1.15)} 100%{transform:scale(1)} }
    .modal-success h3 { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .modal-success p { font-size: 13px; color: var(--muted); margin-bottom: 1.5rem; }
    .success-btns { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
    .btn-continue {
      background: #f0f4f8; color: var(--text); border: none; border-radius: 9px;
      padding: 10px 20px; font-size: 13px; font-weight: 700;
      cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-continue:hover { background: #dde5ee; }
    .btn-view-cart {
      background: var(--blue); color: white; border: none; border-radius: 9px;
      padding: 10px 20px; font-size: 13px; font-weight: 700;
      cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-view-cart:hover { background: var(--blue-dark); }

    /* ===== ORDER SUCCESS (TikTok Shop Style) ===== */
    .order-success { display: none; }
    .order-success-header {
      background: linear-gradient(135deg, var(--green), #1e8449);
      border-radius: 18px 18px 0 0; padding: 2.5rem 1.5rem 1.5rem;
      text-align: center; position: relative;
    }
    .order-success-checkwrap {
      width: 70px; height: 70px; background: white;
      border-radius: 50%; display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      animation: pop 0.4s ease;
    }
    .order-success-checkwrap i { font-size: 2rem; color: var(--green); }
    .order-success-header h3 { color: white; font-size: 20px; font-weight: 800; margin-bottom: 4px; }
    .order-success-header p { color: rgba(255,255,255,0.85); font-size: 13px; }

    .order-success-body { padding: 1.25rem 1.5rem; }
    .order-notif-hint {
      display: flex; align-items: center; gap: 10px;
      background: #f0f7ff; border: 1.5px solid #c8dff5;
      border-radius: 10px; padding: 11px 14px; margin-bottom: 1.25rem;
    }
    .order-notif-hint i { color: var(--blue); font-size: 18px; flex-shrink: 0; }
    .order-notif-hint span { font-size: 12.5px; color: var(--text); line-height: 1.5; }
    .order-notif-hint strong { color: var(--blue); }

    .order-summary-box {
      background: #f8fafc; border-radius: 10px; padding: 12px 14px; margin-bottom: 1.25rem;
    }
    .order-summary-box .summary-row {
      display: flex; justify-content: space-between; align-items: center;
      font-size: 13px; padding: 4px 0;
    }
    .order-summary-box .summary-row .label { color: var(--muted); }
    .order-summary-box .summary-row .val { font-weight: 700; color: var(--text); }
    .order-summary-box .summary-row .val.green { color: var(--green); font-size: 15px; }
    .order-summary-divider { border: none; border-top: 1px solid var(--border); margin: 6px 0; }

    .order-success-btns { display: flex; gap: 8px; }
    .btn-order-continue {
      flex: 1; background: #f0f4f8; color: var(--text); border: none; border-radius: 10px;
      padding: 12px; font-size: 13px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-order-continue:hover { background: #dde5ee; }
    .btn-view-orders {
      flex: 1; background: var(--green); color: white; border: none; border-radius: 10px;
      padding: 12px; font-size: 13px; font-weight: 700;
      cursor: pointer; text-decoration: none;
      display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-view-orders:hover { background: #219150; }

    /* ===== NOTIFICATION DROPDOWN ===== */
    .notif-wrapper { position: relative; }
    .notif-dropdown {
      display: none; position: absolute; top: calc(100% + 8px); right: 0;
      background: white; border-radius: 14px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
      width: 300px; z-index: 200; overflow: hidden; border: 1px solid var(--border);
    }
    .notif-dropdown.open { display: block; animation: fadeDown 0.2s ease; }
    @keyframes fadeDown { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
    .notif-header {
      padding: 12px 16px; font-size: 13px; font-weight: 800; color: var(--text);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .notif-list { max-height: 260px; overflow-y: auto; }
    .notif-item {
      padding: 12px 16px; border-bottom: 1px solid #f0f4f8;
      display: flex; gap: 10px; align-items: flex-start; transition: background 0.15s;
    }
    .notif-item:hover { background: #f8fafc; }
    .notif-item.unread { background: #f0f7ff; }
    .notif-icon { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .notif-icon.blue  { background: #ddeef8; color: var(--blue); }
    .notif-icon.green { background: #d5f5e3; color: var(--green); }
    .notif-icon.orange{ background: #fdebd0; color: #e67e22; }
    .notif-text { flex: 1; }
    .notif-text strong { font-size: 12px; color: var(--text); display: block; margin-bottom: 2px; }
    .notif-text span   { font-size: 11px; color: var(--muted); }
    .notif-empty { padding: 2rem; text-align: center; color: var(--muted); font-size: 13px; }
    .notif-empty i { font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 8px; }

    /* ===== MOBILE ===== */
    .mobile-menu {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 99;
    }
    .mobile-menu.open { display: block; }
    .mobile-menu-inner {
      background: white; width: 75%; max-width: 280px; height: 100%;
      padding: 1.5rem 1rem; box-shadow: 4px 0 20px rgba(0,0,0,0.15);
      display: flex; flex-direction: column; gap: 0.5rem;
    }
    .mobile-menu-logo { font-size: 18px; font-weight: 800; color: var(--blue); display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; }
    .mobile-menu a {
      display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 10px;
      color: var(--text); text-decoration: none; font-size: 14px; font-weight: 600; transition: background 0.2s;
    }
    .mobile-menu a:hover, .mobile-menu a.active { background: #eef6fc; color: var(--blue); }
    .mobile-menu a i { width: 18px; text-align: center; }

    .spinner {
      width: 18px; height: 18px; border: 3px solid rgba(255,255,255,0.4);
      border-top-color: #fff; border-radius: 50%;
      animation: spin 0.6s linear infinite; display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 1024px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hamburger { display: flex; }
      .product-grid { grid-template-columns: repeat(2, 1fr); gap: 0.85rem; }
      .hero { padding: 3rem 1.25rem 4rem; }
    }
    @media (max-width: 480px) {
      .product-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
      .product-img { height: 120px; font-size: 3rem; }
      nav { padding: 0 1rem; }
      .notif-dropdown { width: 260px; right: -60px; }
      .order-success-btns { flex-direction: column; }
    }
  </style>
</head>
<body>

  <nav>
    <button class="hamburger" onclick="openMobileMenu()"><i class="fa-solid fa-bars"></i></button>
    <a class="nav-logo" href="#"><i class="fa-solid fa-fish"></i> eIsda</a>
    <div class="nav-links">
      <a href="{{ route('customer.shop') }}" class="active"><i class="fa-solid fa-store"></i> Shop</a>
      <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
      <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
      <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    </div>
    <div class="nav-right">
      <div class="notif-wrapper">
        <button class="icon-btn" onclick="toggleNotif(event)" title="Notifications">
          <i class="fa-solid fa-bell"></i>
          <span class="badge" id="notifBadge"></span>
        </button>
        <div class="notif-dropdown" id="notifDropdown">
          <div class="notif-header">
            <span><i class="fa-solid fa-bell" style="margin-right:6px;color:var(--blue)"></i>Notifications</span>
            <span style="font-size:11px;color:var(--muted);font-weight:500;cursor:pointer" onclick="markAllRead()">Mark all read</span>
          </div>
          <div class="notif-list" id="notifList"></div>
        </div>
      </div>

      <a href="{{ route('customer.cart') }}" class="icon-btn" title="Cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="badge" id="cartBadge"></span>
      </a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
      </form>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu" onclick="closeMobileMenu(event)">
    <div class="mobile-menu-inner">
      <div class="mobile-menu-logo"><i class="fa-solid fa-fish"></i> eIsda</div>
      <a href="{{ route('customer.shop') }}" class="active"><i class="fa-solid fa-store"></i> Shop</a>
      <a href="{{ route('customer.announcements') }}"><i class="fa-solid fa-bullhorn"></i> Announcements</a>
      <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> My Orders</a>
      <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
    </div>
  </div>

  <div class="hero">
    <div class="hero-content">
      <h1><i class="fa-solid fa-fish"></i> Welcome to eIsda</h1>
      <p>Fresh, Quality Seafood — Straight from the Source!</p>
      <a href="#products" class="btn-hero" onclick="scrollToProducts(event)">
        <i class="fa-solid fa-bag-shopping"></i> Shop Now
        <i class="fa-solid fa-chevron-down" style="margin-left:4px;font-size:11px;opacity:0.8;"></i>
      </a>
    </div>
  </div>

  <div class="container" id="products">
    <div class="section-header">
      <div class="section-title">
        <i class="fa-solid fa-bowl-food" style="color:var(--blue)"></i>
        Available Fish Products
        <span class="product-count">{{ $products->count() }}</span>
      </div>
    </div>

    <div class="product-grid">
      @foreach($products as $product)
      <div class="product-card">
        <div class="product-img">
          @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"/>
          @else
            <i class="fa-solid {{ $product->icon ?? 'fa-fish' }}"></i>
          @endif
          <div class="img-overlay"></div>
          @if(isset($product->stock) && $product->stock > 0)
            <span class="stock-badge">In Stock</span>
          @else
            <span class="out-of-stock-badge">Out of Stock</span>
          @endif
        </div>
        <div class="product-info">
          <div class="product-name">{{ $product->name }}</div>
          <div class="product-price">
            ₱{{ number_format($product->price) }}
            <span class="product-unit">/ kg</span>
          </div>
          @if(isset($product->stock))
            @if($product->stock > 0)
              <div class="stock-remaining {{ $product->stock <= 5 ? 'low' : '' }}">
                <i class="fa-solid fa-scale-balanced"></i>
                {{ number_format($product->stock, 2) }} kg remaining
              </div>
            @else
              <div class="stock-remaining out"><i class="fa-solid fa-ban"></i> Out of Stock</div>
            @endif
          @endif
          <div class="btn-group">
            <button class="btn-add"
              onclick="openAtcModal({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->price }},'{{ $product->image ? asset('storage/'.$product->image) : '' }}',{{ $product->stock ?? 999 }})"
              {{ isset($product->stock) && $product->stock <= 0 ? 'disabled' : '' }}>
              <i class="fa-solid fa-cart-plus"></i> Add
            </button>
            <button class="btn-order-card"
              onclick="openOrderModal({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->price }},'{{ $product->image ? asset('storage/'.$product->image) : '' }}',{{ $product->stock ?? 999 }})"
              {{ isset($product->stock) && $product->stock <= 0 ? 'disabled' : '' }}>
              <i class="fa-solid fa-clipboard-list"></i> Order
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  <!-- ===== ADD TO CART MODAL ===== -->
  <div class="modal-overlay" id="atcModal">
    <div class="modal-card">
      <button class="modal-close" onclick="closeAtcModal()"><i class="fa-solid fa-xmark"></i></button>
      <div id="atcForm">
        <div class="modal-img-area" id="atcImgArea">
          <i class="fa-solid fa-fish" id="atcIcon"></i>
          <img id="atcImg" src="" alt="" style="display:none"/>
          <span class="modal-prod-badge" id="atcProductLabel">Product</span>
        </div>
        <div class="modal-body">
          <div class="modal-title"><i class="fa-solid fa-cart-plus" style="color:var(--blue)"></i> Add to Cart</div>
          <div class="modal-stock-info" id="atcStockInfo">
            <i class="fa-solid fa-scale-balanced"></i>
            <span id="atcStockText">— kg remaining</span>
          </div>
          <div class="price-display">
            <div>
              <div class="label">Total Price</div>
              <div class="total" id="atcTotal">₱0.00</div>
            </div>
            <div>
              <div class="per-unit" id="atcPerUnit">₱0/kg</div>
              <div class="per-unit" id="atcQtyLabel">× 1 kg</div>
            </div>
          </div>
          <div class="qty-control">
            <label><i class="fa-solid fa-scale-balanced"></i> Quantity (kg)</label>
            <div class="qty-btns">
              <button class="qty-btn" type="button" onclick="changeAtcQty(-1)">−</button>
              <span class="qty-num" id="atcQtyNum">1</span>
              <button class="qty-btn" type="button" onclick="changeAtcQty(1)">+</button>
            </div>
          </div>
          <button class="btn-confirm" id="atcSubmitBtn" type="button" onclick="confirmAtc()">
            <i class="fa-solid fa-circle-check"></i> Add to Cart
          </button>
        </div>
      </div>
      <div class="modal-success" id="atcSuccess">
        <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h3>Added to Cart!</h3>
        <p id="atcSuccessMsg"></p>
        <div class="success-btns">
          <button class="btn-continue" type="button" onclick="closeAtcModal()"><i class="fa-solid fa-store"></i> Continue Shopping</button>
          <a class="btn-view-cart" href="{{ route('customer.cart') }}"><i class="fa-solid fa-cart-shopping"></i> View Cart</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== ORDER MODAL ===== -->
  <div class="modal-overlay" id="orderModal">
    <div class="modal-card">
      <button class="modal-close" onclick="closeOrderModal()"><i class="fa-solid fa-xmark"></i></button>

      {{-- ORDER FORM --}}
      <div id="orderForm">
        <div class="modal-img-area" id="orderImgArea">
          <i class="fa-solid fa-fish" id="orderIcon"></i>
          <img id="orderImg" src="" alt="" style="display:none"/>
          <span class="modal-prod-badge" id="orderProductLabel">Product</span>
        </div>
        <div class="modal-body">
          <div class="modal-title"><i class="fa-solid fa-clipboard-list" style="color:var(--blue)"></i> Place Order</div>
          <div class="modal-stock-info" id="orderStockInfo">
            <i class="fa-solid fa-scale-balanced"></i>
            <span id="orderStockText">— kg remaining</span>
          </div>
          <div class="price-display">
            <div>
              <div class="label">Total Price</div>
              <div class="total" id="orderTotal">₱0.00</div>
            </div>
            <div>
              <div class="per-unit" id="orderPerUnit">₱0/kg</div>
              <div class="per-unit" id="orderQtyLabel">× 1 kg</div>
            </div>
          </div>
          <div class="qty-control">
            <label><i class="fa-solid fa-scale-balanced"></i> Quantity (kg)</label>
            <div class="qty-btns">
              <button class="qty-btn" type="button" onclick="changeOrderQty(-1)">−</button>
              <span class="qty-num" id="orderQtyNum">1</span>
              <button class="qty-btn" type="button" onclick="changeOrderQty(1)">+</button>
            </div>
          </div>
          <div class="modal-field">
            <label><i class="fa-solid fa-user"></i> Full Name</label>
            <input type="text" id="orderName" placeholder="e.g. Juan dela Cruz" value="{{ auth()->user()->full_name ?? '' }}"/>
          </div>
          <div class="modal-field">
            <label><i class="fa-solid fa-location-dot"></i> Address</label>
            <input type="text" id="orderAddress" placeholder="e.g. Brgy. San Vicente, Calapan" value="{{ auth()->user()->address ?? '' }}"/>
          </div>
          <div class="modal-field">
            <label><i class="fa-solid fa-phone"></i> Contact Number</label>
            <input type="tel" id="orderPhone" placeholder="e.g. 09XX-XXX-XXXX" value="{{ auth()->user()->phone ?? '' }}"/>
          </div>
          <div class="modal-field">
            <label><i class="fa-solid fa-calendar"></i> Pickup Date</label>
            <input type="date" id="orderPickupDate" min="{{ date('Y-m-d') }}"/>
          </div>
          <div class="note-box">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
              <strong>Important:</strong> Please pick up your order on time. Unclaimed orders past the scheduled time will be made available for other customers. eIsda reserves the right to release unclaimed reservations.
            </div>
          </div>
          <button class="btn-confirm green" id="orderSubmitBtn" type="button" onclick="confirmOrder()">
            <i class="fa-solid fa-circle-check"></i> Place Order
          </button>
        </div>
      </div>

      {{-- ORDER SUCCESS (TikTok Shop style) --}}
      <div class="order-success" id="orderSuccess">
        <div class="order-success-header">
          <div class="order-success-checkwrap">
            <i class="fa-solid fa-check"></i>
          </div>
          <h3>Thank you for your order!</h3>
          <p>Your order has been placed successfully.</p>
        </div>
        <div class="order-success-body">
          <div class="order-notif-hint">
            <i class="fa-solid fa-bell"></i>
            <span>You'll receive updates in your <strong>notifications inbox</strong>. We'll notify you when your order is confirmed.</span>
          </div>
          <div class="order-summary-box">
            <div class="summary-row">
              <span class="label">Order #</span>
              <span class="val" id="successOrderId">—</span>
            </div>
            <div class="summary-row">
              <span class="label">Product</span>
              <span class="val" id="successProduct">—</span>
            </div>
            <div class="summary-row">
              <span class="label">Quantity</span>
              <span class="val" id="successQty">—</span>
            </div>
            <hr class="order-summary-divider"/>
            <div class="summary-row">
              <span class="label">Total</span>
              <span class="val green" id="successTotal">—</span>
            </div>
          </div>
          <div class="order-success-btns">
            <button class="btn-order-continue" type="button" onclick="closeOrderModal()">
              <i class="fa-solid fa-store"></i> Continue Shopping
            </button>
            <a class="btn-view-orders" href="{{ route('customer.orders') }}">
              <i class="fa-solid fa-clipboard-list"></i> View Orders
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    /* ── State ── */
    let atcProductId = null, atcProduct = '', atcPrice = 0, atcQty = 1, atcStock = 0, atcProductImg = '';
    let orderProductId = null, orderProduct = '', orderPrice = 0, orderQty = 1, orderStock = 0;

    /* ── LocalStorage cart (for badge only) ── */
    function getCart()   { return JSON.parse(localStorage.getItem('eisda_cart') || '[]'); }
    function saveCart(c) { localStorage.setItem('eisda_cart', JSON.stringify(c)); updateBadges(); }

    function updateBadges() {
      const n  = getCart().length;
      const cb = document.getElementById('cartBadge');
      cb.textContent   = n;
      cb.style.display = n > 0 ? 'flex' : 'none';
      const notifs = getNotifs();
      const unread = notifs.filter(x => x.unread).length;
      const nb = document.getElementById('notifBadge');
      nb.textContent   = unread || '';
      nb.style.display = unread > 0 ? 'flex' : 'none';
    }

    function scrollToProducts(e) {
      e.preventDefault();
      document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function setModalImage(iconEl, imgEl, imgSrc) {
      if (imgSrc) { iconEl.style.display='none'; imgEl.src=imgSrc; imgEl.style.display='block'; }
      else        { iconEl.style.display='block'; imgEl.style.display='none'; }
    }

    function renderStockInfo(el, textEl, stock) {
      el.className = 'modal-stock-info';
      if (stock <= 0)      { el.classList.add('out'); textEl.textContent = 'Out of Stock'; }
      else if (stock <= 5) { el.classList.add('low'); textEl.textContent = stock.toFixed(2) + ' kg remaining — Mabilis mauubos!'; }
      else                 { textEl.textContent = stock.toFixed(2) + ' kg remaining'; }
    }

    /* ══════════════════════════════
       ADD TO CART MODAL
    ══════════════════════════════ */
    function openAtcModal(productId, product, price, imgSrc, stock) {
      atcProductId = productId; atcProduct = product; atcPrice = price; atcStock = stock; atcQty = 1; atcProductImg = imgSrc;
      setModalImage(document.getElementById('atcIcon'), document.getElementById('atcImg'), imgSrc);
      document.getElementById('atcProductLabel').textContent = product;
      renderStockInfo(document.getElementById('atcStockInfo'), document.getElementById('atcStockText'), stock);
      updateAtcPrice();
      document.getElementById('atcForm').style.display    = 'block';
      document.getElementById('atcSuccess').style.display = 'none';
      const btn = document.getElementById('atcSubmitBtn');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Add to Cart';
      document.getElementById('atcModal').classList.add('active');
    }
    function closeAtcModal() { document.getElementById('atcModal').classList.remove('active'); }
    function changeAtcQty(delta) {
      atcQty = Math.max(1, Math.min(atcStock || 9999, atcQty + delta));
      document.getElementById('atcQtyNum').textContent = atcQty;
      updateAtcPrice();
    }
    function updateAtcPrice() {
      document.getElementById('atcTotal').textContent    = '₱' + (atcQty * atcPrice).toLocaleString('en-PH', {minimumFractionDigits:2});
      document.getElementById('atcPerUnit').textContent  = '₱' + atcPrice + '/kg';
      document.getElementById('atcQtyLabel').textContent = '× ' + atcQty + ' kg';
    }

    async function confirmAtc() {
      const btn = document.getElementById('atcSubmitBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Adding...';
      try {
        const res = await fetch('{{ route("customer.cart.add") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
          body: JSON.stringify({ product_id: atcProductId, quantity: atcQty })
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Failed to add.');

        // Update local badge
        const cart = getCart();
        const ex   = cart.find(c => c.product_id === atcProductId);
        if (ex) ex.qty += atcQty; else cart.push({ id: Date.now(), product_id: atcProductId, product: atcProduct, qty: atcQty, price: atcPrice, image: atcProductImg });
        saveCart(cart);

        addNotif({ type:'blue', icon:'fa-cart-plus', title: atcQty+'kg ng '+atcProduct+' naidagdag sa cart', time:'Ngayon', unread:true });

        document.getElementById('atcSuccessMsg').textContent = atcQty + 'kg ng ' + atcProduct + ' (₱' + (atcQty*atcPrice).toLocaleString() + ') naidagdag sa iyong cart!';
        document.getElementById('atcForm').style.display    = 'none';
        document.getElementById('atcSuccess').style.display = 'block';
      } catch(err) {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Add to Cart';
      }
    }
    document.getElementById('atcModal').addEventListener('click', function(e){ if(e.target===this) closeAtcModal(); });

    /* ══════════════════════════════
       ORDER MODAL
    ══════════════════════════════ */
    function openOrderModal(productId, product, price, imgSrc, stock) {
      orderProductId = productId; orderProduct = product; orderPrice = price; orderStock = stock; orderQty = 1;
      setModalImage(document.getElementById('orderIcon'), document.getElementById('orderImg'), imgSrc);
      document.getElementById('orderProductLabel').textContent = product;
      renderStockInfo(document.getElementById('orderStockInfo'), document.getElementById('orderStockText'), stock);
      updateOrderPrice();
      document.getElementById('orderForm').style.display    = 'block';
      document.getElementById('orderSuccess').style.display = 'none';
      const btn = document.getElementById('orderSubmitBtn');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Place Order';
      document.getElementById('orderModal').classList.add('active');
    }
    function closeOrderModal() { document.getElementById('orderModal').classList.remove('active'); }
    function changeOrderQty(delta) {
      orderQty = Math.max(1, Math.min(orderStock || 9999, orderQty + delta));
      document.getElementById('orderQtyNum').textContent = orderQty;
      updateOrderPrice();
    }
    function updateOrderPrice() {
      document.getElementById('orderTotal').textContent    = '₱' + (orderQty * orderPrice).toLocaleString('en-PH', {minimumFractionDigits:2});
      document.getElementById('orderPerUnit').textContent  = '₱' + orderPrice + '/kg';
      document.getElementById('orderQtyLabel').textContent = '× ' + orderQty + ' kg';
    }

    async function confirmOrder() {
      const name       = document.getElementById('orderName').value.trim();
      const address    = document.getElementById('orderAddress').value.trim();
      const phone      = document.getElementById('orderPhone').value.trim();
      const pickupDate = document.getElementById('orderPickupDate').value;

      if (!name)       { alert('Pakienter ang iyong pangalan.'); document.getElementById('orderName').focus(); return; }
      if (!address)    { alert('Pakienter ang iyong address.'); document.getElementById('orderAddress').focus(); return; }
      if (!phone)      { alert('Pakienter ang iyong contact number.'); document.getElementById('orderPhone').focus(); return; }
      if (!pickupDate) { alert('Pakipili ng pickup date.'); document.getElementById('orderPickupDate').focus(); return; }

      const btn = document.getElementById('orderSubmitBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Placing Order...';

      try {
        const res = await fetch('{{ route("customer.orders.store") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
          body: JSON.stringify({
            name:   name,
            phone:       phone,
            address:     address,
            pickup_date: pickupDate,
            items: [{ key: 'shop_' + Date.now(), product_id: orderProductId, quantity: orderQty }]
          })
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to place order.');

        // Add notification
        addNotif({ type:'green', icon:'fa-clipboard-list', title:'Order #'+data.order_id+' — '+orderQty+'kg '+orderProduct+' placed!', time:'Ngayon', unread:true });

        // Fill success screen
        document.getElementById('successOrderId').textContent = '#' + data.order_id;
        document.getElementById('successProduct').textContent = orderProduct;
        document.getElementById('successQty').textContent     = orderQty + ' kg';
        document.getElementById('successTotal').textContent   = '₱' + (orderQty * orderPrice).toLocaleString('en-PH', {minimumFractionDigits:2});

        document.getElementById('orderForm').style.display    = 'none';
        document.getElementById('orderSuccess').style.display = 'block';

        // Auto-reload after 3 seconds para ma-update ang stock
        setTimeout(() => location.reload(), 3000);

        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Place Order';

      } catch(err) {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Place Order';
      }
    }
    document.getElementById('orderModal').addEventListener('click', function(e){ if(e.target===this) closeOrderModal(); });

    /* ══════════════════════════════
       NOTIFICATIONS (localStorage)
    ══════════════════════════════ */
    function getNotifs()   { return JSON.parse(localStorage.getItem('eisda_notifs') || '[]'); }
    function saveNotifs(n) { localStorage.setItem('eisda_notifs', JSON.stringify(n)); updateBadges(); }

    function addNotif(notif) {
      const notifs = getNotifs();
      notifs.unshift({ ...notif, id: Date.now() });
      if (notifs.length > 30) notifs.pop();
      saveNotifs(notifs);
      renderNotifs();
    }

    function renderNotifs() {
      const list   = document.getElementById('notifList');
      const notifs = getNotifs();
      if (!notifs.length) {
        list.innerHTML = '<div class="notif-empty"><i class="fa-solid fa-bell-slash"></i>Wala pang notification</div>';
        return;
      }
      list.innerHTML = notifs.map(n => `
        <div class="notif-item ${n.unread ? 'unread' : ''}">
          <div class="notif-icon ${n.type}"><i class="fa-solid ${n.icon}"></i></div>
          <div class="notif-text">
            <strong>${n.title}</strong>
            <span>${n.time}</span>
          </div>
        </div>`).join('');
    }

    function markAllRead() {
      saveNotifs(getNotifs().map(n => ({...n, unread:false})));
      renderNotifs();
    }

    function toggleNotif(e) {
      e.stopPropagation();
      const dd = document.getElementById('notifDropdown');
      dd.classList.toggle('open');
      if (dd.classList.contains('open')) markAllRead();
    }
    document.addEventListener('click', () => document.getElementById('notifDropdown').classList.remove('open'));

    /* ── Mobile menu ── */
    function openMobileMenu()  { document.getElementById('mobileMenu').classList.add('open'); }
    function closeMobileMenu(e) {
      if (e.target === document.getElementById('mobileMenu') || e.target.closest('a'))
        document.getElementById('mobileMenu').classList.remove('open');
    }

    updateBadges();
    renderNotifs();
  </script>
</body>
</html>