<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      background: linear-gradient(180deg, #2a7db5 0%, #1a5a8a 40%, #0e3d5c 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-wrapper {
      width: 360px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.4rem;
    }
    .logo span { color: white; font-size: 24px; font-weight: 700; }
    h2 { color: white; font-size: 32px; font-weight: 600; }
    .card {
      background: white;
      border-radius: 14px;
      padding: 2.5rem 2rem;
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 1.1rem;
    }
    .field-label { font-size: 15px; color: #555; margin-bottom: 6px; }
    .card input[type="email"],
    .card input[type="password"],
    .card input[type="text"] {
      width: 100%;
      padding: 12px 40px 12px 14px;
      border: 1px solid #d0dce8;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      background: white;
      color: #2c3e50;
    }
    .error-msg {
      background: #fde8e8;
      color: #c0392b;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
    }
    .password-wrap { position: relative; }
    .clear-icon {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 15px;
      cursor: pointer;
      user-select: none;
    }
    .clear-icon:hover { color: #2a7db5; }
    .remember-row { display: flex; align-items: center; gap: 10px; }
    .remember-row input { width: 16px; height: 16px; }
    .remember-row label { font-size: 14px; color: #555; }
    .btn-login {
      width: 100%;
      background: #2a7db5;
      color: white;
      border: none;
      border-radius: 9px;
      padding: 14px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 0.25rem;
    }
    .btn-login:hover { background: #1f6090; }
    .signup-link { font-size: 14px; color: rgba(255,255,255,0.85); text-align: center; }
    .signup-link a { color: #7ec8f0; text-decoration: none; font-weight: 600; }
  </style>
</head>
<body>
  <div class="login-wrapper">

    

    <h2>Log In</h2>
    <div class="card">
      @if ($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div>
          <div class="field-label">Email</div>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus />
        </div>
        <div style="margin-top: 1.1rem;">
          <div class="field-label">Password</div>
          <div class="password-wrap">
            <input type="password" name="password" id="pass" required />
            <span class="clear-icon" onclick="togglePass()">
              <i id="passIcon" class="fa-regular fa-eye-slash"></i>
            </span>
          </div>
        </div>
        <div class="remember-row" style="margin-top: 1.1rem;">
          <input type="checkbox" name="remember" id="remember" />
          <label for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn-login" style="margin-top: 1.1rem;">Log In</button>
      </form>
    </div>
    <p class="signup-link">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
  </div>

  <script>
    function togglePass() {
      const input = document.getElementById('pass');
      const icon = document.getElementById('passIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      }
    }
  </script>

</body>
</html>