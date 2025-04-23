<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ trans('backpack::base.error_page.title', ['error' => $error_number]) }}</title>
        <style>
          .error_number {
              font-size: 156px;
              font-weight: 600;
              line-height: 100px;
          }
          .error_number small {
              font-size: 56px;
              font-weight: 700;
          }

          .error_number hr {
              margin-top: 60px;
              margin-bottom: 0;
              width: 50px;
          }

          .error_title {
              margin-top: 40px;
              font-size: 36px;
              font-weight: 400;
          }

          .error_description {
              font-size: 24px;
              font-weight: 400;
          }

          .center {
              height: 100vh;
              display: flex;
              justify-content: center;
              align-items: center;
              text-align: center;
          }
        </style>
    </head>
    <body>
      <div class="center">
        @yield('content')
      </div>
    </body>
</html>
