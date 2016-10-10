<!doctype html>
<html lang='zh'>
<head>
    <title>为你读诗后台管理登录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css');?>">
    <script src="<?php echo asset('js/jquery.min.js');?>"></script>
    <script src="<?php echo asset('js/bootstrap.min.js');?>"></script>
    <style type="text/css">
    body {
      padding-top: 140px;
      padding-bottom: 40px;
      background-color: #eee;
    }

    .form-signin {
      max-width: 330px;
      padding: 15px;
      margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
      margin-bottom: 10px;
    }
    .form-signin .checkbox {
      font-weight: normal;
    }
    .form-signin .form-control {
      position: relative;
      height: auto;
      -webkit-box-sizing: border-box;
         -moz-box-sizing: border-box;
              box-sizing: border-box;
      padding: 10px;
      font-size: 16px;
    }
    .form-signin .form-control:focus {
      z-index: 2;
    }
    .form-signin input[type="email"] {
      margin-bottom: -1px;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
      margin-bottom: 10px;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }
  </style>
</head>
<body>
    <div class="container">
      <form class="form-signin" role="form" action="{{url('admin/login')}}" method='post'>
        <h2 class="form-signin-heading">请登录</h2>
        <input type="username" name="username" class="form-control" placeholder="用户名" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="密　码" required>
        <input type="text" name='valiatecode' class="form-control"  placeholder="验证码" required>
        <img src="{{url('sdfiwjeijfis/captcha')}}" onclick="refresh()" >看不清，点击图片刷新
        <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
      </form>
    </div> <!-- /container -->
</body>
<script>
  function refresh()
  {
    window.location.reload();
  }
</script>
</html>