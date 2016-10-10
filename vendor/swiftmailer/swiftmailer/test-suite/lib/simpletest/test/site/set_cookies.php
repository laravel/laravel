<?php
    $parts = parse_url($_SERVER['PHP_SELF']);
    $path = pathinfo($parts['path']);
    setcookie("session_cookie", "A");
    setcookie("short_cookie", "B", time() + 100, "/");
    setcookie("day_cookie", "C", time() + 24 * 3600, "/");
    setcookie("path_cookie", "D", time() + 100, $path['dirname'] . "/path/");
    setcookie("hour_cookie", "E", time() + 3600, "/");
?><html>
    <head><title>SimpleTest testing links</title></head>
    <body>
        <p>
            A target for the
            <a href="http://www.lastcraft.com/simple_test.php">SimpleTest</a>
            test suite.
            All it does is set some cookies which you can see
            <a href="network_confirm.php">here</a>.
        </p>
    </body>
</html>
