<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>eIsda - Register</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      background: linear-gradient(180deg, #2a7db5 0%, #1a5a8a 40%, #0e3d5c 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 0;
    }
    .register-wrapper {
      width: 360px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.4rem;
    }
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
    .card input[type="text"],
    .card input[type="email"],
    .card input[type="tel"],
    .card input[type="password"],
    .card textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #d0dce8;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      background: white;
      color: #2c3e50;
      font-family: 'Segoe UI', sans-serif;
    }
    .card textarea {
      resize: vertical;
      min-height: 70px;
    }
    .error-msg {
      background: #fde8e8;
      color: #c0392b;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 13px;
    }
    .section-label {
      text-align: center;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.5px;
      color: #aab8c8;
      text-transform: uppercase;
      margin-bottom: -0.4rem;
    }
    .two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.8rem;
    }
    .btn-register {
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
    .btn-register:hover { background: #1f6090; }
    .login-link { font-size: 14px; color: rgba(255,255,255,0.85); text-align: center; }
    .login-link a { color: #7ec8f0; text-decoration: none; font-weight: 600; }
  </style>
</head>
<body>
  <div class="register-wrapper">

    <h2>Register</h2>

    <div class="card">
      @if ($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
          <div class="field-label">Full Name</div>
          <input type="text" name="full_name" value="{{ old('name') }}" placeholder="Juan Dela Cruz" required />
        </div>

        <div style="margin-top: 1.1rem;">
          <div class="field-label">Email Address</div>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required />
        </div>

        <div style="margin-top: 1.1rem;">
          <div class="field-label">Phone Number</div>
          <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="09XXXXXXXXX" required />
        </div>

        <div style="margin-top: 1.1rem;">
          <div class="field-label">Address</div>
          <textarea name="address" placeholder="House No., Street, Barangay, City, Province" required>{{ old('address') }}</textarea>
        </div>

        <div class="two-col" style="margin-top: 0.2rem;">
          <div>
            <div class="field-label">Password</div>
            <input type="password" name="password" placeholder="Min. 8 chars" required />
          </div>
          <div>
            <div class="field-label">Confirm Password</div>
            <input type="password" name="password_confirmation" placeholder="Re-enter" required />
          </div>
        </div>

        <button type="submit" class="btn-register" style="margin-top: 0.5rem;">Register</button>
      </form>
    </div>

    <p class="login-link">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
  </div>
</body>
</html>