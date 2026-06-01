<?php
header('content-type: text/event-stream');
header('cache-control: no-cache');
header('connection: keep-alive');

if (function_exists('pcntl_async_signals')) {
    pcntl_async_signals(true);

    // Listen for SIGINT (Ctrl+C) and SIGTERM (System termination)
    $shutdownHandler = function () {
        // Echo a final closing frame to the browser, then exit instantly
        echo "data: close\n\n";
        @flush();
        exit(0);
    };

    pcntl_signal(SIGINT, $shutdownHandler);
    pcntl_signal(SIGTERM, $shutdownHandler);
}

ignore_user_abort(true);

while (ob_get_level()) {
    ob_end_flush();
}
flush();

$state_file = "./../../../../.storage/last_change.txt";

$connection_time = file_exists($state_file) ? (float)file_get_contents($state_file) : microtime(true);

$last_heartbeat = time();

while (true) {
    echo ": tick\n\n";

    if (@flush() === false || connection_aborted() || connection_status() !== CONNECTION_NORMAL) {
        // Clean up or log if you want, then break the loop to free the PHP thread immediately
        exit();
    }

    if(file_exists($state_file)) {
        $last_change = (float)file_get_contents($state_file);
        if($last_change > $connection_time) {
            echo "data: reload\n\n";
            flush();
            exit();
        }
    }

    if(time() - $last_heartbeat >= 3) {
        echo ": heartbeat\n\n";
        flush();
        $last_heartbeat = time();
    }

    usleep(500000);
}