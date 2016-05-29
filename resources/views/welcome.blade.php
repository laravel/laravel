<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100vh;
                width:100vw;
                overflow:hidden;
            }

            body {
                margin: 0;
                padding: 0;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
                font-size: 96px;
                font-size: 150px;
            }
            svg text {
                text-anchor: middle;
                vertical-align:middle;
            }
            svg mask rect {
                fill: rgba(255, 255, 255, 1);
            }
            svg > rect {
                fill: white;
                -webkit-mask: url(#mask);
                mask: url(#mask);
            }
            body {
                background: -webkit-linear-gradient(90deg, #e55d87, #5fc3e4);
                background: linear-gradient(0deg, #e55d87, #5fc3e4);
                background-size: 400% 400%;
                -webkit-animation: Gradient 4s linear infinite;
                        animation: Gradient 4s linear infinite;
            }
            @-webkit-keyframes Gradient {
                0%{background-position:50% 0%}
                50%{background-position:50% 100%}
                100%{background-position:50% 0%}
            }
            @keyframes Gradient {
                0%{background-position:50% 0%}
                50%{background-position:50% 100%}
                100%{background-position:50% 0%}
            }
        </style>
    </head>
    <body>
      <!-- inspred from http://codepen.io/SahAssar/pen/ZYOJOM -- svg for cross browser -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080" width="100%" height="100%" preserveAspectRatio="xMidYMid slice">
        <defs>
          <mask id="mask" x="0" y="0" width="100%" height="100%" >
            <rect x="0" y="0" width="100%" height="100%"/>
            <text x="960" y="55%">Laravel 5</text>
          </mask>
        </defs>
        <rect x="0" y="0" width="100%" height="100%"/>
      </svg>
    </body>
</html>
