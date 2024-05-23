<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Now</title>
</head>
<style>
.login-link-error{
    padding: 15px 25px;
    font-size: 25px;
    color: white;
    border: 2px solid white;
    text-decoration: none;
}
</style>
<body style="margin:0px;">
    <section style="padding-top: 15%; background: linear-gradient(45deg, rgba(0, 255, 235, 1) 0%, rgba(7, 58, 187, 1) 100%); min-height: 80vh; ">
        <div style="text-align: center; filter: invert(1);"><img src="{{asset('assets/img/thumbs-up.png')}}" alt="" width="100px"></div>
        <div style="text-align: center;">
            <h2 style="color: white; font-size:45px;">Your Account Is successfully Activated</h2>
            <p style="color: white; font-size: 20px"><a style="" class="login-link-error" href="{{url('login')}}">Login Here</a></p>
        </div>
    </section>
</body>
</html>