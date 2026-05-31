<?php
require_once ("./squeak_util/vendor/autoload.php");

use Spatie\Watcher\Watch;

Watch::paths("./core", "./public_html")
    ->onAnyChange(function (string $path) {
        file_put_contents("./.storage/last_change.txt", microtime(true));
    })
    ->start();
