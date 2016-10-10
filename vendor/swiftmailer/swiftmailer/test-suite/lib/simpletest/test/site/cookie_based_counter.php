<?php
    $count = 1;
    if (isset($_COOKIE['count'])) {
        $count = $_COOKIE['count'] + 1;
    }
    setcookie('count', $count);
?><html>
    <head><title>Cookie Counter</title></head>
    <body><?php print 'Count: ' . $count; ?></body>
</html>