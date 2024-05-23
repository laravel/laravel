<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      /* background-image: url('{{asset('assets/img/registration-back.jpg')}}'); */
      background-color: white;
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      overflow: hidden;
    }
    #background-slider {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      opacity: 0;
      transition: opacity 1s ease;
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
    }
    .container {
      max-width: 600px;
      margin-top: 20px;
      margin-inline: auto;
      padding: 20px;
    }
    footer {
    /* background-color: #333; */
    color: black;
    }
    header, footer {
      /* color: #fff; */
      text-align: left;
      padding: 10px 50px;
    }
    header img{
        width: 200px;
        height: auto;
        padding: 15px;
        background: #ffffff90;
    }
    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
    }
    footer p{
        color: black;
    }
    form {
      /* background-color: #f2f2f2; */
      /* background-color: #ffeab3f0; */
      padding-inline: 30px;
      padding-block: 30px;
      border-radius: 8px;
      box-shadow: none;
    }
    input{
        height: 40px;
        border:  none;
        border-bottom: 2px solid white;
        color: white;
        font-size: 16px;
        font-weight: 300;
        letter-spacing: 0.0525rem; 
    }
    input::placeholder{
        height: 40px;
        border:  none;
        border-bottom: 2px solid white;
        color: #ffffff80;
        font-size: 16px;
        font-weight: 300;
        letter-spacing: 0.0525rem; 
    }
    input:focus-visible{
      border:none;
      border-bottom: 2px solid white;
      box-shadow: none;
      outline: none !important;
    }
    input[type="text"], input[type="email"], input[type="number"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      background: transparent;
      box-sizing: border-box;
      box-shadow: none;
      /* border: 1px solid #ccc; */
      border-radius: 0px;
      color: white;
        font-size: 16px;
        font-weight: 300;
        letter-spacing: 0.0525rem; 
    }
    input[type="submit"] {
      background-color: transparent;
      color: #fff;
      padding: 10px 25px;
      border: 2px solid;
      border-radius: 0px;
      cursor: pointer;
      height: unset;
      font-size: 16px;
    }
    input[type="submit"]:hover {
      background-color: #555;
    }
    .inbox{
        width: 50%;
    }
    .twinbox{
        display: flex;
        gap: 20px;
    }
    .form-title{
        text-transform: uppercase;
        text-align: left;
        font-size: 28px;
        font-weight: 600;
        color: white;
        letter-spacing: 0.0525rem;
    }
    .container label{
        font-size: 14px;
        font-weight: 300;
        color: white;
        letter-spacing: 0.0525rem;
    }
    .sub-btn{
      text-align: left;
      margin-block: 20px;
    }
    .login-txt{
        text-align: left;
        font-size: 13px;
        font-weight: 300;
        color: white;
        letter-spacing: 0.0525rem;
    }
    .top-mr-2{
        margin-top: 10px;
    }
    .star{
        color: red;
    }
    .backbtn{
        text-align: end;
        margin-top: -25px
    }
    .login-link{
      font-size: 14px;
        font-weight: 600;
        color: white;
        letter-spacing: 0.0525rem;
    }
    .onebox{
        margin-top: 30px;
    }
    .container-main{
      min-height: 100vh !important;
    }
    .container-main .row{
      display: flex;
    }
    .container-main .row .col-8-main{
      width: 60%;
    }
    .container-main .row .col-4-main{
      width: 40%;
      text-align: left;
      background: linear-gradient(135deg, rgb(28 103 183 / 80%), rgb(3 87 158 / 80%), rgb(11 53 98 / 80%));
      padding-bottom: 100px;
      padding-top: 25px;
    }
    #password{
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 2px solid white;
      color: white;
    }
    .sub-btn {
        text-align: left;
        background: none;
        margin-block: 20px;
        padding: 10px 20px;
        color: white;
        border: 2px solid white;
        font-size: 14px;
        letter-spacing: 0.0525rem;
        cursor: pointer;
    }
    .sub-btn:hover{
        color: black;
        background: white;
        transition: all 0.5s ease;
    }
    .alert.alert-success{
        color: white;
        margin-left: 30px;
        background: #ffffff50;
        display: inline;
        padding: 7px 15px;
        letter-spacing: 0.0525rem;
    }
  </style>
</head>
<body>

<div id="background-slider"></div>

<div class="container-main">
  <div class="row">
    <div class="col-8-main">

    </div>
    <div class="col-4-main" style="min-height: 100vh;">
        <header style="opacity: 1 !important; ">
          <img src="../assets/img/logo.gif" alt="Company Logo" height="50"> <!-- Replace "logo.png" with your logo path -->
        </header>
        <div class="container">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <h2 class="form-title">Forgot Password</h2>
                        <div class="form-group row" style="display:block;">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter Your Registered Email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary sub-btn">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>      

    </div>
  </div>
</div>
       

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const images = ["{{asset('assets/img/registration-back.jpg')}}", "{{asset('assets/img/register-img-2.jpg')}}", "{{asset('assets/img/register-img-3.jpg')}}"];
      let currentIndex = 0;
      const backgroundSlider = document.getElementById('background-slider');

      function changeBackground() {
          backgroundSlider.style.backgroundImage = `url('${images[currentIndex]}')`;
          currentIndex = (currentIndex + 1) % images.length;
          backgroundSlider.style.opacity = 1;

          setTimeout(() => {
              backgroundSlider.style.opacity = 0.5;
          }, 3000); // Duration of fade out transition

          setTimeout(changeBackground, 3500); // Total duration including fade in and out (1000ms + 5000ms)
      }

      changeBackground();
  });

</script>

</body>
</html>


