<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda - Announcements</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; min-height: 100vh; }

    :root {
      --blue: #2a7db5;
      --blue-dark: #1a5a8a;
      --blue-deeper: #0e3d5c;
      --red: #e74c3c;
      --bg: #f0f4f8;
      --text: #1a3a52;
      --muted: #6b7f8e;
      --border: #d0dce8;
    }

    nav {
      background: linear-gradient(90deg, var(--blue), var(--blue-dark));
      padding: 0 1.5rem; display: flex; align-items: center;
      justify-content: space-between; height: 60px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.18);
      position: relative;
    }
    .nav-logo { color: white; font-size: 19px; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 8px; }
    .nav-links { display: flex; gap: 0.25rem; position: absolute; left: 50%; transform: translateX(-50%); }
    .nav-links a {
      color: rgba(255,255,255,0.85); text-decoration: none; padding: 7px 13px;
      border-radius: 8px; font-size: 13px; font-weight: 500;
      transition: background 0.2s; display: flex; align-items: center; gap: 5px;
      position: relative;
    }
    .nav-links a:hover { background: rgba(255,255,255,0.15); color: white; }
    .nav-links a.active { background: rgba(255,255,255,0.22); color: white; font-weight: 700; }
    .nav-right { display: flex; align-items: center; gap: 0.5rem; }
    .btn-logout {
      background: rgba(255,255,255,0.13); color: white;
      border: 1px solid rgba(255,255,255,0.28); border-radius: 8px;
      padding: 6px 13px; font-size: 12px; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; gap: 6px; font-family: inherit;
    }
    .btn-logout:hover { background: rgba(255,255,255,0.22); }

    /* Ann badge */
    .ann-badge {
      display: none;
      position: absolute;
      top: 4px; right: 4px;
      background: var(--red);
      width: 8px; height: 8px;
      border-radius: 50%;
      border: 1.5px solid #1a5a8a;
    }

    .hero {
      background: linear-gradient(135deg, var(--blue) 0%, var(--blue-deeper) 100%);
      color: white; text-align: center; padding: 2.5rem 1rem;
      position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-content { position: relative; z-index: 1; }
    .hero h1 { font-size: 26px; font-weight: 800; margin-bottom: 0.4rem; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .hero p { opacity: 0.75; font-size: 14px; }

    .content { max-width: 750px; margin: 2rem auto; padding: 0 1rem; }

    .ann-card {
      background: white; border-radius: 12px; padding: 1.5rem;
      margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      border-left: 4px solid var(--blue);
    }
    .ann-card.warning { border-left-color: #f59e0b; }
    .ann-card.promo   { border-left-color: #10b981; }

    .ann-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
    .ann-type {
      font-size: 11px; font-weight: 700; padding: 3px 10px;
      border-radius: 20px; text-transform: uppercase;
      display: flex; align-items: center; gap: 5px;
    }
    .ann-type.info    { background: #ddeef8; color: #1a3a52; }
    .ann-type.warning { background: #fff3cd; color: #856404; }
    .ann-type.promo   { background: #d4f5e9; color: #0a6640; }
    .ann-date { font-size: 12px; color: #aaa; }
    .ann-content { font-size: 15px; color: #333; line-height: 1.6; }

    .ann-card.new-ann { box-shadow: 0 2px 12px rgba(42,125,181,0.18); }
    .new-label {
      font-size: 10px; font-weight: 700; background: var(--red);
      color: white; padding: 2px 7px; border-radius: 20px; margin-left: 6px;
    }

    .empty { text-align: center; color: #aaa; padding: 3rem; font-size: 15px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .empty i { font-size: 2.5rem; opacity: 0.3; }
  </style>
</head>
<body>

<nav>
  <a class="nav-logo" href="{{ route('customer.shop') }}">
    <i class="fa-solid fa-fish"></i> eIsda
  </a>
  <div class="nav-links">
    <a href="{{ route('customer.shop') }}"><i class="fa-solid fa-store"></i> Shop</a>
    <a href="{{ route('customer.announcements') }}" class="active" id="annLink">
      <i class="fa-solid fa-bullhorn"></i> Announcements
      <span class="ann-badge" id="annBadge"></span>
    </a>
    <a href="{{ route('customer.orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
    <a href="{{ route('customer.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
  </div>
  <div class="nav-right">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Log Out
      </button>
    </form>
  </div>
</nav>

<div class="hero">
  <div class="hero-content">
    <h1><i class="fa-solid fa-bullhorn"></i> Announcements</h1>
    <p>Latest updates from eIsda</p>
  </div>
</div>

<div class="content">
  @forelse($announcements->where('is_active', true) as $ann)
    <div class="ann-card {{ $ann->type }}" data-date="{{ $ann->created_at->toISOString() }}">
      <div class="ann-header">
        <div style="display:flex; align-items:center; gap:6px;">
          <span class="ann-type {{ $ann->type }}">
            @if($ann->type === 'info')
              <i class="fa-solid fa-circle-info"></i> Info
            @elseif($ann->type === 'warning')
              <i class="fa-solid fa-triangle-exclamation"></i> Warning
            @else
              <i class="fa-solid fa-tag"></i> Promo
            @endif
          </span>
          <span class="new-label" style="display:none;">NEW</span>
        </div>
        <span class="ann-date">{{ $ann->created_at->format('M d, Y') }}</span>
      </div>
      <div class="ann-content">{{ $ann->content }}</div>
    </div>
  @empty
    <div class="empty">
      <i class="fa-solid fa-bullhorn"></i>
      No announcements yet.
    </div>
  @endforelse
</div>

<script>
  const LAST_SEEN_KEY = 'ann_last_seen';
  const lastSeen = localStorage.getItem(LAST_SEEN_KEY);

  // Markahan ang mga bagong announcement at ipakita ang NEW label
  document.querySelectorAll('.ann-card').forEach(card => {
    const cardDate = card.getAttribute('data-date');
    if (cardDate && (!lastSeen || new Date(lastSeen) < new Date(cardDate))) {
      card.classList.add('new-ann');
      card.querySelector('.new-label').style.display = 'inline-block';
    }
  });

  // I-clear ang badge at i-save ang current time bilang last seen
  localStorage.setItem(LAST_SEEN_KEY, new Date().toISOString());
  document.getElementById('annBadge').style.display = 'none';
</script>

</body>
</html>