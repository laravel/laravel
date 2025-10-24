// Lightweight user module for avatar + settings state
(function () {
  const AVATAR_KEY = 'mie.avatarDataUrl';
  const TOGGLE_PREFIX = 'mie.toggle.';

  function defaultAvatarDataUrl() {
    const svg = `<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'>
      <defs>
        <linearGradient id='g' x1='0' x2='1' y1='0' y2='1'>
          <stop offset='0%' stop-color='#136d96'/>
          <stop offset='100%' stop-color='#124191'/>
        </linearGradient>
      </defs>
      <rect width='80' height='80' fill='url(#g)'/>
      <circle cx='40' cy='32' r='14' fill='#fff' opacity='0.95'/>
      <rect x='16' y='50' width='48' height='18' rx='9' fill='#fff' opacity='0.95'/>
    </svg>`;
    return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
  }

  function getAvatar() {
    return localStorage.getItem(AVATAR_KEY) || defaultAvatarDataUrl();
  }

  function setAvatar(src) {
    try { localStorage.setItem(AVATAR_KEY, src); } catch (_) {}
    updateAvatars();
  }

  function updateAvatars() {
    const src = getAvatar();
    document.querySelectorAll('.profile-avatar').forEach(img => { img.src = src; });
  }

  function initAvatarUpload() {
    const input = document.getElementById('avatarInput');
    if (!input) return;
    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = () => setAvatar(reader.result);
      reader.readAsDataURL(file);
    });
  }

  function getToggle(key, fallback) {
    const v = localStorage.getItem(TOGGLE_PREFIX + key);
    if (v === null) return !!fallback;
    return v === 'true';
  }

  function setToggle(key, value) {
    try { localStorage.setItem(TOGGLE_PREFIX + key, value ? 'true' : 'false'); } catch (_) {}
  }

  function initToggles() {
    document.querySelectorAll('input[type="checkbox"][data-key]').forEach(chk => {
      const key = chk.getAttribute('data-key');
      chk.checked = getToggle(key, chk.checked);
      chk.addEventListener('change', () => setToggle(key, chk.checked));
    });
  }

  function init() {
    updateAvatars();
    initAvatarUpload();
    initToggles();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();

