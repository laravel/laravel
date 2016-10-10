<?php

/* vim: set shiftwidth=2 expandtab softtabstop=2: */

/**
 * Custom autoloader for non-composer installations.
 */
spl_autoload_register(function($class) {
  if ($class[0] == '\\') {
    $class = substr($class, 1);
  }

  $path = sprintf('%s/%s.php', __DIR__, implode('/', explode('\\', $class)));

  if (is_file($path)) {
    require_once($path);
  }
});
