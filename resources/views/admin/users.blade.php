<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda Admin - Users</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; min-height: 100vh; }
    .sidebar { width: 240px; background: linear-gradient(180deg, #0e2a3a 0%, #0e3d5c 100%); min-height: 100vh; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; }
    .sidebar-logo { padding: 1.5rem; color: white; font-size: 20px; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar-logo span { font-size: 13px; display: block; opacity: 0.6; font-weight: 400; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 1rem 0; }
    .nav-item a { display: flex; align-items: center; gap: 12px; padding: 12px 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
    .nav-item a:hover { background: rgba(255,255,255,0.08); color: white; }
    .nav-item a.active { background: rgba(42,125,181,0.4); color: white; border-left: 3px solid #2a7db5; }
    .nav-icon { font-size: 16px; width: 20px; text-align: center; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
    .btn-logout { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.6); font-size: 14px; cursor: pointer; background: none; border: none; width: 100%; padding: 8px 0; }
    .btn-logout:hover { color: white; }
    .main { margin-left: 240px; flex: 1; }
    .topbar { background: white; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .topbar h1 { font-size: 18px; font-weight: 700; color: #1a3a52; }
    .content { padding: 2rem; }
    .alert-success { background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 1rem; font-size: 14px; }
    .empty-state { text-align: center; padding: 3rem; color: #999; font-size: 14px; }
    .table-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #f7fafc; }
    th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase; }
    td { padding: 12px 16px; font-size: 14px; color: #444; border-top: 1px solid #f0f0f0; vertical-align: middle; }
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: #ddeef8; display: flex; align-items: center; justify-content: center; font-size: 16px; }

    /* Search bar */
    .search-bar {
      padding: 1rem 1.2rem;
      border-bottom: 1px solid #f0f0f0;
      display: flex; align-items: center; gap: 0.75rem;
      flex-wrap: wrap;
    }
    .search-wrap {
      position: relative; flex: 1; min-width: 200px;
    }
    .search-wrap input {
      width: 100%; padding: 9px 12px 9px 36px;
      border: 1.5px solid #e2e8f0; border-radius: 8px;
      font-size: 13px; font-family: inherit; outline: none;
      transition: border-color 0.2s;
    }
    .search-wrap input:focus { border-color: #2a7db5; }
    .search-wrap .search-icon {
      position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
      color: #94a3b8; font-size: 14px;
    }
    .filter-select {
      padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
      font-size: 13px; font-family: inherit; outline: none; background: white;
      color: #334155; cursor: pointer; transition: border-color 0.2s;
    }
    .filter-select:focus { border-color: #2a7db5; }
    .user-count {
      font-size: 13px; color: #94a3b8; margin-left: auto;
    }
    .user-count span { font-weight: 700; color: #1a3a52; }

    /* Hidden row */
    tr.hidden-row { display: none; }

    /* No results */
    .no-results { display: none; text-align: center; padding: 2.5rem; color: #94a3b8; font-size: 14px; }
    .no-results.show { display: block; }
  </style>
</head>
<body>
  <aside class="sidebar">
    <div class="sidebar-logo">🐟 eIsda<span>Admin Panel</span></div>
    <nav class="sidebar-nav">
      <div class="nav-item"><a href="{{ route('admin.dashboard') }}"><span class="nav-icon">📊</span> Dashboard</a></div>
      <div class="nav-item"><a href="{{ route('admin.products') }}"><span class="nav-icon">🐟</span> Products</a></div>
      <div class="nav-item"><a href="{{ route('admin.orders') }}"><span class="nav-icon">📋</span> Orders</a></div>
      <div class="nav-item"><a href="{{ route('admin.users') }}" class="active"><span class="nav-icon">👥</span> Users</a></div>
      <div class="nav-item"><a href="{{ route('admin.history') }}"><span class="nav-icon">🕒</span> History</a></div>
      <div class="nav-item"><a href="{{ route('admin.financial') }}"><span class="nav-icon">💰</span> Financial</a></div>
      <div class="nav-item"><a href="{{ route('admin.announcements') }}"><span class="nav-icon">📢</span> Announcements</a></div>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">🚪 Log Out</button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar"><h1>Customers</h1></div>
    <div class="content">

      @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
      @endif

      <div class="table-card">

        <!-- Search Bar -->
        <div class="search-bar">
          <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." oninput="filterUsers()"/>
          </div>
          <select class="filter-select" id="sortSelect" onchange="filterUsers()">
            <option value="all">All Users</option>
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="name">Sort by Name</option>
          </select>
          <div class="user-count">Showing <span id="visibleCount">{{ $users->count() }}</span> of {{ $users->count() }} users</div>
        </div>

        @if($users->isEmpty())
          <div class="empty-state">👥 No customers registered yet.</div>
        @else
          <table id="usersTable">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered</th>
              </tr>
            </thead>
            <tbody id="usersBody">
              @foreach($users as $user)
              <tr class="user-row"
                data-name="{{ strtolower($user->full_name ?? $user->name) }}"
                data-email="{{ strtolower($user->email) }}"
                data-phone="{{ $user->phone ?? '' }}"
                data-date="{{ $user->created_at->timestamp }}">
                <td><div class="avatar">👤</div></td>
                <td>{{ $user->full_name ?? $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '—' }}</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="no-results" id="noResults">😕 No users found matching your search.</div>
        @endif

      </div>
    </div>
  </div>

  <script>
    function filterUsers() {
      const search = document.getElementById('searchInput').value.toLowerCase().trim();
      const sort   = document.getElementById('sortSelect').value;
      const tbody  = document.getElementById('usersBody');
      const rows   = [...tbody.querySelectorAll('.user-row')];

      // Filter
      let visible = rows.filter(row => {
        const name  = row.dataset.name;
        const email = row.dataset.email;
        const phone = row.dataset.phone;
        return name.includes(search) || email.includes(search) || phone.includes(search);
      });

      // Sort
      if (sort === 'newest') {
        visible.sort((a, b) => b.dataset.date - a.dataset.date);
      } else if (sort === 'oldest') {
        visible.sort((a, b) => a.dataset.date - b.dataset.date);
      } else if (sort === 'name') {
        visible.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
      }

      // Hide all first
      rows.forEach(r => r.classList.add('hidden-row'));

      // Show sorted visible
      visible.forEach(r => {
        r.classList.remove('hidden-row');
        tbody.appendChild(r);
      });

      // Update count
      document.getElementById('visibleCount').textContent = visible.length;

      // No results message
      document.getElementById('noResults').classList.toggle('show', visible.length === 0);
    }
  </script>
</body>
</html>