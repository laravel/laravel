<?php
    if (count($HTTP_COOKIE_VARS) > 0) {
        $_COOKIE = $HTTP_COOKIE_VARS;
    }
    if (count($HTTP_GET_VARS) > 0) {
        $_GET = $HTTP_GET_VARS;
    }
    if (count($HTTP_POST_VARS) > 0) {
        $_POST = $HTTP_POST_VARS;
    }
    if (! isset($_SERVER)) {
        $_SERVER = $HTTP_SERVER_VARS;
    }
    global $HTTP_RAW_POST_DATA;
    
    require_once('page_request.php');
?><html>
    <head><title>Simple test target file</title></head>
    <body>
        A target for the SimpleTest test suite.
        <h1>Request</h1>
        <dl>
            <dt>Protocol version</dt><dd><?php print $_SERVER['SERVER_PROTOCOL']; ?></dd>
            <dt>Request method</dt><dd><?php print $_SERVER['REQUEST_METHOD']; ?></dd>
            <dt>Accept header</dt><dd><?php print $_SERVER['HTTP_ACCEPT']; ?></dd>
        </dl>
        <h1>Cookies</h1>
        <?php
            if (count($_COOKIE) > 0) {
                foreach ($_COOKIE as $key => $value) {
                    print htmlentities($key) . "=[" . htmlentities($value) . "]<br />\n";
                }
            }
        ?>
        <h1>Raw GET data</h1>
        <?php
            print "[" . $_SERVER['QUERY_STRING'] . "]";
        ?>
        <h1>GET data</h1>
        <?php
            $get = PageRequest::get();
            if (count($get) > 0) {
                foreach ($get as $key => $value) {
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    print htmlentities($key) . "=[" . htmlentities($value) . "]<br />\n";
                }
            }
        ?>
        <h1>Dump of $_GET data</h1>
        <?php
            print '<pre>';
            print_r($_GET);
            print '</pre>';
        ?>
        <h1>Raw POST data</h1>
        <?php
            print "[" . $HTTP_RAW_POST_DATA . "]";
        ?>
        <pre><?php print_r(PageRequest::post()); ?></pre>
        <h1>POST data</h1>
        <?php
            function show_array_value($array) {
                $html = "";
                foreach ($array as $key => $value) {
                    $html .= htmlentities($key) . "=[";
                    if (is_array($value)) {
                        $html .= show_array_value($value);
                    } else {
                        $html .= htmlentities($value);
                    }
                    $html .= "]";
                }
                
                return $html;
            }

            if (count($_POST) > 0) {
                echo show_array_value($_POST)."<br />\n";
            }
        ?>
    </body>
</html>
