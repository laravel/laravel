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
    .forgot-password-link {
        color: white;
        font-size: 14px;
        text-transform: uppercase;
        text-decoration: none;
        letter-spacing: 0.0525rem;
    }
    .forgot-password-link:hover{
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div id="background-slider"></div>

<div class="container-main">
  <div class="row">
    <div class="col-8-main">

    </div>
    <div class="col-4-main">
        <header style="opacity: 1 !important; ">
          <img src="../assets/img/logo.gif" alt="Company Logo" height="50"> <!-- Replace "logo.png" with your logo path -->
        </header>
        <div class="container">
              <form method="POST" action="{{ route('login') }}">
                    @csrf

                <!-- <div style="text-align:center;">
                  <img src="../assets/img/logo.gif" alt="Company Logo" height="50">
                </div> -->
                <h2 class="form-title">LOGIN</h2>

                <div class="backbtn">
                  <a href="https://stage.webshark.in/iimatm2024/abstracts/"><img style="background-color: white; padding: 3px; border-radius: 50%;" src="{{asset('assets/img/back.png')}}" alt=""></a>
                </div>
                <div class="onebox top-mr-2">
                <label for="name">Email</label><span class="star">*</span>
                <input type="email" id="eamil" placeholder="Enter Registered Email" name="email">
                </div>
                <div class="onebox top-mr-2" style="position:relative;">
                  <label for="name">Password</label><span class="star">*</span>
                  <input type="password" id="password" placeholder="Your Password" name="password">
                  <span class="toggle-password" style="display: inline;position: absolute;right: 0;bottom: 10px;">
                      <img src="{{asset('assets/img/eye.png')}}" alt="" width="20px" height="auto" style="filter:invert(1);">
                  </span>
                </div>
                <div class="onebox top-mr-2">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password-link">
                            Forgot Your Password?
                        </a>
                    @endif
                </div>
                <div class="sub-btn">
                    <input type="submit" value="LOGIN">
                </div>
                <div class="login-txt">
                  <p> Interested to Register for an IIM ATM 2024? <a style="" class="login-link" href="{{url('register')}}">REGISTER HERE</a></p>
                </div>
              </form>
        </div>

    </div>
  </div>
</div>
       

<!-- <footer>
  <p>&copy; IIM ATM 2024</p>
</footer> -->




<!-- <script>
  document.addEventListener("DOMContentLoaded", function() {
    const images = ["{{asset('assets/img/registration-back.jpg')}}", "{{asset('assets/img/register-img-2.jpg')}}", "{{asset('assets/img/register-img-3.jpg')}}"]; // Add your image URLs here
    let currentIndex = 0;

    // function changeBackground() {
    //     document.body.style.backgroundImage = `url('${images[currentIndex]}')`;
    //     document.body.style.backgroundSize = "cover";
    //     document.body.style.backgroundPosition = "center";

    //     setTimeout(() => {
    //         // Apply Ken Burns effect by shifting background position
    //         document.body.style.transition = "background-position 5s";
    //         document.body.style.backgroundPosition = "50% 50%";

    //         setTimeout(() => {
    //             // Reset background position for the next image
    //             document.body.style.transition = "none";
    //             document.body.style.backgroundPosition = "center";
    //             currentIndex = (currentIndex + 1) % images.length;
    //             changeBackground();
    //         }, 3000); // Transition duration + delay
    //     }, 1000); // Delay before starting the effect
    // }

    function changeBackground() {
        // Fade out the previous image
        document.body.style.opacity = 0;

        setTimeout(() => {
            // Change the background image
            document.body.style.backgroundImage = `url('${images[currentIndex]}')`;
            currentIndex = (currentIndex + 1) % images.length;

            // Fade in the new image
            document.body.style.opacity = 1;
        }, 1000); // Duration of fade out transition

        setTimeout(changeBackground, 6000); // Total duration including fade in and out (1000ms + 5000ms)
    }

    changeBackground();
  });

</script> -->

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
<!-- to change the view of password -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye / eye-slash icon
            // this.querySelector('i').classList.toggle('fa-eye');
            // this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>

</body>
</html>


