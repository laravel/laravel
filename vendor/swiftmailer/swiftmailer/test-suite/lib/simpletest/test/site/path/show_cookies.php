<?php
    if (count($HTTP_COOKIE_VARS) > 0) {
        $_COOKIE = $HTTP_COOKIE_VARS;
    }
?><html>
    <head><title>Simple test target file</title></head>
    <body>
        A target for the SimpleTest test suite that displays cookies.
        <h1>Cookies</h1>
        <?php
            if (count($_COOKIE) > 0) {
                foreach ($_COOKIE as $key => $value) {
                    print $key . "=" . $value . ";";
                }
            }
        ?>
    </body>
</html>