<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin – Products</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; min-height: 100vh; }

    .sidebar {
      width: 240px; background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%);
      min-height: 100vh; display: flex; flex-direction: column;
      position: fixed; top: 0; left: 0; z-index: 300;
      transition: transform 0.28s ease;
    }
    .sidebar.hidden { transform: translateX(-100%); }
    .sidebar-logo {
      padding: 1.25rem 1.5rem; color: white; font-size: 19px; font-weight: 700;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      display: flex; align-items: center; gap: 10px;
    }
    .sidebar-logo small { font-size: 11px; display: block; opacity: 0.5; font-weight: 400; margin-top: 1px; }
    .sidebar-nav { flex: 1; padding: 0.75rem 0; overflow-y: auto; }
    .nav-item a {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 1.5rem; color: rgba(255,255,255,0.7);
      text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;
    }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-item a i { width: 18px; text-align: center; font-size: 14px; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
    .btn-logout {
      display: flex; align-items: center; gap: 10px;
      color: rgba(255,255,255,0.6); font-size: 14px;
      cursor: pointer; background: none; border: none; width: 100%; padding: 8px 0;
    }
    .btn-logout:hover { color: white; }

    .sidebar-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.45); z-index: 299;
    }
    .sidebar-overlay.show { display: block; }

    .topbar {
      position: sticky; top: 0; z-index: 200;
      background: white; padding: 0.85rem 1.5rem;
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .topbar-left { display: flex; align-items: center; gap: 12px; }
    .hamburger {
      display: none; background: none; border: none;
      font-size: 20px; color: #1a3a52; cursor: pointer; padding: 4px;
    }
    .topbar h1 { font-size: 17px; font-weight: 700; color: #1a3a52; display: flex; align-items: center; gap: 8px; }
    .btn-add {
      background: #2a7db5; color: white; border: none; border-radius: 9px;
      padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; gap: 6px; transition: background 0.2s; white-space: nowrap;
    }
    .btn-add:hover { background: #1f6090; }

    .layout { display: flex; }
    .main { margin-left: 240px; flex: 1; min-width: 0; transition: margin-left 0.28s ease; }

    .content { padding: 1.5rem; }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .stat-card {
      background: white; border-radius: 12px; padding: 1rem 1.1rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      display: flex; align-items: center; gap: 12px;
    }
    .stat-icon {
      width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center; font-size: 17px;
    }
    .stat-icon.blue   { background: #ddeef8; color: #2a7db5; }
    .stat-icon.green  { background: #d5f5e3; color: #27ae60; }
    .stat-icon.red    { background: #fde8e8; color: #e74c3c; }
    .stat-info .label { font-size: 10px; color: #6b7f8e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-info .value { font-size: 22px; font-weight: 800; color: #1a3a52; line-height: 1.1; }

    .table-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; }
    .table-header h2 { font-size: 14px; font-weight: 700; color: #1a3a52; display: flex; align-items: center; gap: 7px; }
    .table-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 560px; }
    thead { background: #f7fafc; }
    th { padding: 11px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap; }
    td { padding: 11px 14px; font-size: 13px; color: #444; border-top: 1px solid #f0f0f0; vertical-align: middle; }
    tbody tr:hover { background: #fafcff; }

    .prod-img-cell { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid #e0eaf2; display: block; }
    .prod-img-placeholder {
      width: 44px; height: 44px; border-radius: 8px;
      background: linear-gradient(135deg, #ddeef8, #c5e0f5);
      display: flex; align-items: center; justify-content: center;
      color: #2a7db5; font-size: 18px;
    }

    .badge-stock { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .badge-stock.in  { background: #d4f5e9; color: #0a6640; }
    .badge-stock.out { background: #fde8e8; color: #c0392b; }

    .btn-edit   { background: #e8f4fd; color: #2a7db5; border: none; border-radius: 7px; padding: 6px 11px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; margin-right: 4px; transition: background 0.2s; white-space: nowrap; }
    .btn-edit:hover { background: #cce3f5; }
    .btn-delete { background: #fde8e8; color: #c0392b; border: none; border-radius: 7px; padding: 6px 11px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s; white-space: nowrap; }
    .btn-delete:hover { background: #f5c6c6; }

    .empty-state { text-align: center; padding: 3.5rem 2rem; color: #6b7f8e; }
    .empty-state i { font-size: 2.8rem; opacity: 0.3; margin-bottom: 0.75rem; display: block; }
    .empty-state p { font-size: 13px; }

    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5); z-index: 1000;
      align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.show { display: flex; }
    .modal-card {
      background: white; border-radius: 16px; width: 100%; max-width: 480px;
      box-shadow: 0 16px 60px rgba(0,0,0,0.22);
      animation: slideUp 0.22s ease; max-height: 94vh; overflow-y: auto;
    }
    @keyframes slideUp { from { transform: translateY(24px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header {
      padding: 1.2rem 1.4rem; border-bottom: 1px solid #eee;
      display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: white; z-index: 1;
    }
    .modal-header h2 { font-size: 15px; font-weight: 800; color: #1a3a52; display: flex; align-items: center; gap: 8px; }
    .modal-close { background: #f0f4f8; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #666; cursor: pointer; }
    .modal-close:hover { background: #dde5ee; }
    .modal-body { padding: 1.3rem 1.4rem; }
    .modal-footer { padding: 1rem 1.4rem; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: flex-end; }

    .field { margin-bottom: 1rem; }
    .field label { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: #6b7f8e; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .field input {
      width: 100%; padding: 10px 12px;
      border: 1.5px solid #d0dce8; border-radius: 9px;
      font-size: 14px; font-family: inherit; color: #1a3a52; outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .field input:focus { border-color: #2a7db5; box-shadow: 0 0 0 3px rgba(42,125,181,0.1); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .img-upload-area {
      border: 2px dashed #d0dce8; border-radius: 10px;
      padding: 1.1rem; text-align: center; cursor: pointer;
      transition: border-color 0.2s, background 0.2s; position: relative;
    }
    .img-upload-area:hover { border-color: #2a7db5; background: #f0f7ff; }
    .img-upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .img-upload-area .upload-icon { font-size: 1.8rem; color: #b0c4d8; margin-bottom: 6px; display: block; }
    .img-upload-area p { font-size: 12px; color: #6b7f8e; }
    .img-upload-area p span { color: #2a7db5; font-weight: 600; }
    .img-preview { width: 100%; max-height: 130px; object-fit: cover; border-radius: 8px; margin-top: 8px; display: none; border: 1px solid #d0dce8; }

    .btn-cancel { background: #f0f4f8; color: #555; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
    .btn-cancel:hover { background: #dde5ee; }
    .btn-save { background: #2a7db5; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; }
    .btn-save:hover:not(:disabled) { background: #1f6090; }
    .btn-save:disabled { background: #b0c4d8; cursor: not-allowed; }

    .confirm-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; padding: 1rem; }
    .confirm-overlay.show { display: flex; }
    .confirm-card { background: white; border-radius: 14px; padding: 2rem 1.5rem; max-width: 340px; width: 100%; text-align: center; box-shadow: 0 16px 60px rgba(0,0,0,0.2); }
    .confirm-card .confirm-icon { font-size: 2.8rem; color: #e74c3c; margin-bottom: 0.85rem; }
    .confirm-card h3 { font-size: 16px; font-weight: 800; color: #1a3a52; margin-bottom: 6px; }
    .confirm-card p { font-size: 13px; color: #6b7f8e; margin-bottom: 1.4rem; line-height: 1.5; }
    .confirm-btns { display: flex; gap: 10px; justify-content: center; }
    .btn-no  { background: #f0f4f8; color: #555; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; cursor: pointer; }
    .btn-yes { background: #e74c3c; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; }
    .btn-yes:hover { background: #c0392b; }

    .toast {
      position: fixed; bottom: 1.5rem; right: 1.5rem;
      background: #1a3a52; color: white;
      padding: 11px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
      display: flex; align-items: center; gap: 8px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      z-index: 9999; opacity: 0; transform: translateY(10px);
      transition: all 0.28s; pointer-events: none; max-width: calc(100vw - 3rem);
    }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.success { background: #27ae60; }
    .toast.error   { background: #e74c3c; }

    .spinner { width: 15px; height: 15px; border: 2.5px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; display: inline-block; flex-shrink: 0; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 900px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .main { margin-left: 0; }
      .hamburger { display: flex; }
      .stats-row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
      .content { padding: 1rem; }
      .topbar { padding: 0.75rem 1rem; }
      .topbar h1 { font-size: 15px; }
      .btn-add span { display: none; }
      .btn-add { padding: 9px 12px; }
      .stats-row { grid-template-columns: repeat(2, 1fr); gap: 0.65rem; }
      .stat-card { padding: 0.85rem; gap: 10px; }
      .stat-icon { width: 36px; height: 36px; font-size: 15px; }
      .stat-info .value { font-size: 18px; }
      .table-wrap { overflow-x: visible; }
      table, thead, tbody, th, td, tr { display: block; }
      thead { display: none; }
      tbody tr {
        background: white; border-radius: 10px; margin-bottom: 0.75rem;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07); padding: 0.85rem;
        display: grid; grid-template-columns: 52px 1fr auto;
        grid-template-areas:
          "img name actions"
          "img meta actions";
        gap: 0 10px; border-top: none !important;
      }
      tbody tr:hover { background: white; }
      td:nth-child(1) { grid-area: img; display: flex; align-items: center; }
      td:nth-child(2) { grid-area: name; font-weight: 700; color: #1a3a52; font-size: 14px; padding: 0; display: flex; align-items: flex-end; }
      td:nth-child(3), td:nth-child(4), td:nth-child(5) { grid-area: meta; padding: 0; font-size: 12px; color: #6b7f8e; display: inline; }
      td:nth-child(3)::after { content: ' · '; }
      td:nth-child(4)::after { content: ' · '; }
      td:nth-child(6) { grid-area: meta; padding: 2px 0 0; display: flex; align-items: flex-start; }
      td:nth-child(7) { grid-area: actions; display: flex; flex-direction: column; gap: 5px; padding: 0; justify-content: center; }
      td:nth-child(7) .btn-edit, td:nth-child(7) .btn-delete { margin: 0; padding: 6px 10px; font-size: 11px; }
      .modal-card { border-radius: 14px; }
      .grid-2 { grid-template-columns: 1fr; gap: 0; }
      .confirm-btns { flex-direction: column; }
      .btn-no, .btn-yes { width: 100%; justify-content: center; }
      .toast { left: 1rem; right: 1rem; bottom: 1rem; }
    }

    @media (max-width: 380px) {
      .stats-row { grid-template-columns: 1fr 1fr; }
      .stat-info .label { font-size: 9px; }
    }
  </style>
</head>
<body>

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <i class="fa-solid fa-fish"></i>
      <div>eIsda <small>Admin Panel</small></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
      <div class="nav-item"><a href="{{ route('admin.products') }}" class="active"><span class="nav-icon">🐟</span> Products</a></div>
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
          <i class="fa-solid fa-right-from-bracket"></i> Log Out
        </button>
      </form>
    </div>
  </aside>

  <div class="main" id="mainContent">

    <div class="topbar">
      <div class="topbar-left">
        <button class="hamburger" onclick="toggleSidebar()">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h1><i class="fa-solid fa-fish" style="color:#2a7db5"></i> Products</h1>
      </div>
      <button class="btn-add" onclick="openModal('add')">
        <i class="fa-solid fa-plus"></i> <span>Add Product</span>
      </button>
    </div>

    <div class="content">

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-box-open"></i></div>
          <div class="stat-info">
            <div class="label">Total</div>
            <div class="value">{{ $products->count() }}</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div class="stat-info">
            <div class="label">In Stock</div>
            <div class="value">{{ $products->where('stock', '>', 0)->count() }}</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-ban"></i></div>
          <div class="stat-info">
            <div class="label">Out of Stock</div>
            <div class="value">{{ $products->where('stock', '<=', 0)->count() }}</div>
          </div>
        </div>
      </div>

      <div class="table-header">
        <h2><i class="fa-solid fa-list" style="color:#2a7db5"></i> All Products</h2>
      </div>
      <div class="table-card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price / kg</th>
                <th>Stock (kg)</th>
                <th>Capital (₱)</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
              <tr id="row-{{ $product->id }}">
                <td>
                  @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="prod-img-cell"/>
                  @else
                    <div class="prod-img-placeholder"><i class="fa-solid fa-fish"></i></div>
                  @endif
                </td>
                <td style="font-weight:600;color:#1a3a52;">{{ $product->name }}</td>
                <td>₱ {{ number_format($product->price, 0) }}</td>
                <td>{{ number_format($product->stock, 0) }} kg</td>
                <td>₱ {{ number_format($product->capital ?? 0, 0) }}</td>
                <td>
                  @if($product->stock > 0)
                    <span class="badge-stock in"><i class="fa-solid fa-circle-check"></i> In Stock</span>
                  @else
                    <span class="badge-stock out"><i class="fa-solid fa-ban"></i> Out of Stock</span>
                  @endif
                </td>
                <td>
                  {{-- FIX: Pass capital as JSON-safe value --}}
                  <button class="btn-edit" onclick="openModal('edit',{{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->price }},{{ $product->stock }},{{ $product->capital ?? 0 }},'{{ $product->image ? asset('storage/'.$product->image) : '' }}')">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                  <button class="btn-delete" onclick="confirmDelete({{ $product->id }},'{{ addslashes($product->name) }}')">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <i class="fa-solid fa-fish"></i>
                    <p>No products yet. Click "+ Add Product" to get started.</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <!-- ADD / EDIT MODAL -->
  <div class="modal-overlay" id="productModal">
    <div class="modal-card">
      <div class="modal-header">
        <h2 id="modalTitle"><i class="fa-solid fa-plus" style="color:#2a7db5"></i> Add Product</h2>
        <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editProductId"/>

        <div class="field">
          <label><i class="fa-solid fa-tag"></i> Product Name</label>
          <input type="text" id="fieldName" placeholder="e.g. Tilapia"/>
        </div>

        <div class="grid-2">
          <div class="field">
            <label><i class="fa-solid fa-peso-sign"></i> Price / kg (₱)</label>
            <input type="number" id="fieldPrice" placeholder="e.g. 160" min="0" step="1"/>
          </div>
          <div class="field">
            <label><i class="fa-solid fa-scale-balanced"></i> Stock (kg)</label>
            <input type="number" id="fieldStock" placeholder="e.g. 100" min="0" step="1"/>
          </div>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-coins"></i> Capital / Cost (₱)</label>
          <input type="number" id="fieldCapital" placeholder="e.g. 10000" min="0" step="1"/>
        </div>

        <div class="field">
          <label><i class="fa-solid fa-image"></i> Product Image</label>
          <div class="img-upload-area">
            <input type="file" id="fieldImage" accept="image/*" onchange="previewImage(this)"/>
            <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
            <p>Drag & drop or <span>browse</span></p>
            <p style="font-size:11px;color:#b0c4d8;margin-top:3px;">JPG, PNG, WEBP — max 2MB</p>
            <img class="img-preview" id="imgPreview"/>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel" onclick="closeModal()"><i class="fa-solid fa-xmark"></i> Cancel</button>
        <button class="btn-save" id="saveBtn" onclick="saveProduct()">
          <i class="fa-solid fa-floppy-disk"></i> Save Product
        </button>
      </div>
    </div>
  </div>

  <!-- DELETE CONFIRM -->
  <div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-card">
      <div class="confirm-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h3>Delete Product?</h3>
      <p id="confirmMsg">Are you sure you want to delete this product?</p>
      <div class="confirm-btns">
        <button class="btn-no" onclick="closeConfirm()"><i class="fa-solid fa-xmark"></i> Cancel</button>
        <button class="btn-yes" id="confirmYesBtn"><i class="fa-solid fa-trash"></i> Delete</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast"></div>

  <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // SIDEBAR
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('show');
    }
    function closeSidebar() {
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.remove('show');
    }

    // TOAST
    function showToast(msg, type = 'success') {
      const t = document.getElementById('toast');
      t.className = 'toast ' + type;
      t.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}"></i> ${msg}`;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3200);
    }

    // IMAGE PREVIEW
    function previewImage(input) {
      const preview = document.getElementById('imgPreview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // MODAL
    let modalMode = 'add';

    function openModal(mode, id = null, name = '', price = '', stock = '', capital = '', imgSrc = '') {
      modalMode = mode;
      document.getElementById('editProductId').value  = id ?? '';
      document.getElementById('fieldName').value      = name;
      document.getElementById('fieldPrice').value     = price;
      document.getElementById('fieldStock').value     = stock;
      // ✅ FIX: Always set capital value correctly (even if 0)
      document.getElementById('fieldCapital').value   = capital;
      document.getElementById('fieldImage').value     = '';

      const preview = document.getElementById('imgPreview');
      if (imgSrc) { preview.src = imgSrc; preview.style.display = 'block'; }
      else         { preview.src = '';    preview.style.display = 'none';  }

      const icon  = mode === 'add' ? 'fa-plus' : 'fa-pen-to-square';
      const label = mode === 'add' ? 'Add Product' : 'Edit Product';
      document.getElementById('modalTitle').innerHTML =
        `<i class="fa-solid ${icon}" style="color:#2a7db5"></i> ${label}`;

      const btn = document.getElementById('saveBtn');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Product';

      document.getElementById('productModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('productModal').classList.remove('show');
    }

    // ✅ FIXED SAVE FUNCTION
    async function saveProduct() {
      const name    = document.getElementById('fieldName').value.trim();
      const price   = document.getElementById('fieldPrice').value.trim();
      const stock   = document.getElementById('fieldStock').value.trim();
      const capital = document.getElementById('fieldCapital').value.trim();
      const imgFile = document.getElementById('fieldImage').files[0];
      const editId  = document.getElementById('editProductId').value;

      if (!name || !price || !stock) {
        showToast('Pakipunan ang Name, Price, at Stock.', 'error');
        return;
      }

      const btn = document.getElementById('saveBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Saving...';

      const fd = new FormData();
      fd.append('name',    name);
      fd.append('price',   price);
      fd.append('stock',   stock);
      // ✅ FIX: Send capital as-is (empty string becomes 0 in controller)
      fd.append('capital', capital !== '' ? capital : '0');
      if (imgFile) fd.append('image', imgFile);

      // ✅ FIX: Idagdag ang _method=PUT para makilala ng Laravel na UPDATE ito
      if (modalMode === 'edit') {
        fd.append('_method', 'PUT');
      }

      // ✅ FIX: Tama na ang URL — store para add, update para edit
      const url = modalMode === 'add'
        ? '{{ route("admin.products.store") }}'
        : `/admin/products/${editId}`;

      try {
        const res  = await fetch(url, {
          method: 'POST', // Lagi POST — ang _method=PUT ang bahala sa routing
          headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
          },
          body: fd,
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save.');

        showToast(data.message, 'success');
        closeModal();
        setTimeout(() => location.reload(), 900);

      } catch (err) {
        showToast(err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Product';
      }
    }

    // DELETE
    let deleteTargetId = null;

    function confirmDelete(id, name) {
      deleteTargetId = id;
      document.getElementById('confirmMsg').textContent =
        `Are you sure you want to delete "${name}"? This cannot be undone.`;
      const btn = document.getElementById('confirmYesBtn');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-trash"></i> Delete';
      document.getElementById('confirmOverlay').classList.add('show');
    }

    function closeConfirm() {
      document.getElementById('confirmOverlay').classList.remove('show');
      deleteTargetId = null;
    }

    document.getElementById('confirmYesBtn').addEventListener('click', async () => {
      if (!deleteTargetId) return;
      const btn = document.getElementById('confirmYesBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Deleting...';

      try {
        const res  = await fetch(`/admin/products/${deleteTargetId}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to delete.');

        showToast('Product deleted successfully.', 'success');
        closeConfirm();
        setTimeout(() => location.reload(), 900);

      } catch (err) {
        showToast(err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Delete';
      }
    });

    document.getElementById('productModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
    document.getElementById('confirmOverlay').addEventListener('click', function(e) {
      if (e.target === this) closeConfirm();
    });
  </script>

</body>
</html>