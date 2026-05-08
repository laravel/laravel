<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin - Announcements</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }
    .sidebar {
      width: 240px; background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%);
      min-height: 100vh; display: flex; flex-direction: column; position: fixed; top: 0; left: 0;
    }
    .sidebar-logo { padding: 1.5rem; color: white; font-size: 20px; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar-logo span { font-size: 13px; display: block; opacity: 0.6; font-weight: 400; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 1rem 0; }
    .nav-item a {
      display: flex; align-items: center; gap: 12px; padding: 12px 1.5rem;
      color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;
    }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-icon { font-size: 16px; width: 20px; text-align: center; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
    .btn-logout {
      display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.6);
      font-size: 14px; cursor: pointer; background: none; border: none; width: 100%; padding: 8px 0; font-family: inherit;
    }
    .btn-logout:hover { color: white; }
    .main { margin-left: 240px; flex: 1; }
    .topbar {
      background: white; padding: 1rem 2rem; display: flex; align-items: center;
      justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .topbar h1 { font-size: 18px; font-weight: 700; color: #1a3a52; }
    .content { padding: 2rem; }
    .section-title { font-size: 15px; font-weight: 700; color: #1a3a52; margin-bottom: 1rem; }
    .table-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f7fafc; }
    th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; }
    td { padding: 12px 16px; font-size: 14px; color: #444; border-top: 1px solid #f0f0f0; vertical-align: middle; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .badge.info    { background: #ddeef8; color: #1a3a52; }
    .badge.warning { background: #fff3cd; color: #856404; }
    .badge.promo   { background: #d4f5e9; color: #0a6640; }
    .badge.active-badge  { background: #d4f5e9; color: #0a6640; }
    .badge.inactive-badge{ background: #fde8e8; color: #c0392b; }
    .btn { padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; font-family: inherit; }
    .btn-primary { background: #2a7db5; color: white; }
    .btn-primary:hover { background: #1f6090; }
    .btn-danger  { background: #fde8e8; color: #c0392b; }
    .btn-danger:hover { background: #f5c6c6; }
    .btn-warning { background: #fff3cd; color: #856404; }
    .btn-warning:hover { background: #ffe69c; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .modal-overlay {
      display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4);
      z-index: 999; align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
      background: white; border-radius: 16px; padding: 2rem; width: 100%; max-width: 500px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .modal h2 { font-size: 16px; font-weight: 700; color: #1a3a52; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%; padding: 10px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px;
      font-size: 14px; font-family: inherit; color: #333; outline: none; transition: border 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #2a7db5; }
    .form-group textarea { resize: vertical; min-height: 100px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem; }
    .btn-cancel { background: #f0f4f8; color: #555; }
    .btn-cancel:hover { background: #e2e8f0; }
    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 14px; font-weight: 500; }
    .alert.success { background: #d4f5e9; color: #0a6640; }
    .actions { display: flex; gap: 6px; }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">🐟 eIsda <span>Admin Panel</span></div>
  <nav class="sidebar-nav">
    <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
    <div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
    <div class="nav-item"><a href="{{ route('admin.orders') }}"><span class="nav-icon">📋</span> Orders</a></div>
    <div class="nav-item"><a href="{{ route('admin.users') }}"><span class="nav-icon">👥</span> Users</a></div>
    <div class="nav-item"><a href="{{ route('admin.history') }}"><span class="nav-icon">🕒</span> History</a></div>
    <div class="nav-item"><a href="{{ route('admin.financial') }}"><span class="nav-icon">💰</span> Financial</a></div>
    <div class="nav-item"><a href="{{ route('admin.announcements') }}" class="active"><span class="nav-icon">📢</span> Announcements</a></div>
  </nav>
  <div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <h1>📢 Announcements</h1>
  </div>
  <div class="content">

    @if(session('success'))
      <div class="alert success">✅ {{ session('success') }}</div>
    @endif

    <div class="top-bar">
      <div class="section-title">All Announcements</div>
      <button class="btn btn-primary" onclick="openCreateModal()">+ New Announcement</button>
    </div>

    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Content</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($announcements as $ann)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td><strong>{{ $ann->title }}</strong></td>
            <td style="max-width:250px;">{{ Str::limit($ann->content, 80) }}</td>
            <td><span class="badge {{ $ann->type }}">{{ ucfirst($ann->type) }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}">
                @csrf @method('PATCH')
                <button type="submit" class="badge btn {{ $ann->is_active ? 'active-badge' : 'inactive-badge' }}" style="cursor:pointer;border:none;">
                  {{ $ann->is_active ? '✅ Active' : '❌ Inactive' }}
                </button>
              </form>
            </td>
            <td>{{ $ann->created_at->format('M d, Y') }}</td>
            <td>
              <div class="actions">
                <button class="btn btn-warning btn-sm" onclick='openEditModal(@json($ann))'>✏️ Edit</button>
                <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}"
                      onsubmit="return confirm('Delete this announcement?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="text-align:center;color:#aaa;padding:2rem;">No announcements yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CREATE MODAL -->
<div class="modal-overlay" id="createModal">
  <div class="modal">
    <h2>📢 New Announcement</h2>
    <form method="POST" action="{{ route('admin.announcements.store') }}">
      @csrf
      <input type="hidden" name="title" value="Announcement"/>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" required placeholder="Write your announcement here..."></textarea>
      </div>
      <input type="hidden" name="type" value="info"/>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeModal('createModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Post Announcement</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h2>✏️ Edit Announcement</h2>
    <form method="POST" id="editForm">
      @csrf @method('PUT')
      <input type="hidden" name="title" id="editTitle"/>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" id="editContent" required></textarea>
      </div>
      <input type="hidden" name="type" id="editType"/>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openCreateModal() {
    document.getElementById('createModal').classList.add('open');
  }
  function openEditModal(ann) {
    document.getElementById('editContent').value = ann.content;
    document.getElementById('editType').value    = ann.type;
    document.getElementById('editForm').action   = '/admin/announcements/' + ann.id;
    document.getElementById('editModal').classList.add('open');
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
  }
  document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
  });
</script>

</body>
</html>