<?php
// Front controller shim for environments where the webroot points to the project root
// (e.g., Plesk httpdocs) instead of the Laravel public/ directory.
require __DIR__.'/public/index.php';