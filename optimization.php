<?php
echo shell_exec('php artisan config:cache');
echo shell_exec('php artisan route:cache');
echo shell_exec('php artisan view:cache');