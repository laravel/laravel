<?php
function my_path() {
    return preg_replace('|/[^/]*.php$|', '/', $_SERVER['SCRIPT_URI']);
}
?>