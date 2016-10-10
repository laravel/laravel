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
    
    require_once('../page_request.php');
?><html>
    <head><title>Simple test front controller</title></head>
    <body>
        Simple test front controller
        <h1>Links</h1>
        <a href="index.php?action=index">Index</a>
        <a href="?action=no_page">No page</a>
        <a href="?action">Bare action</a>
        <a href="?">Empty query</a>
        <a href="">Empty link</a>
        <a href="..">Down one</a>
        
        <h1>Forms</h1>
        <form action="index.php"><input type="submit" name="action" value="Index"></form>
        <form action="."><input type="submit" name="action" value="Same directory"></form>
        <form action=""><input type="submit" name="action" value="Empty action"></form>
        <form><input type="submit" name="action" value="No action"></form>
        <form action=".."><input type="submit" name="action" value="Down one"></form>
        
        <form method="post" action="index.php"><input type="submit" name="action" value="Index post"></form>
        <form method="post" action="."><input type="submit" name="action" value="Same directory post"></form>
        <form method="post" action=""><input type="submit" name="action" value="Empty action post"></form>
        <form method="post"><input type="submit" name="action" value="No action post"></form>
        <form method="post" action=".."><input type="submit" name="action" value="Down one post"></form>
        
        <?php include(dirname(__FILE__) . '/show_request.php'); ?>
    </body>
</html>