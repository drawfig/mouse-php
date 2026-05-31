<?php
header('content-type: text/event-stream');
header('cache-control: no-cache');
header('connection: keep-alive');

while (ob_get_level()) {
    ob_end_flush();
}
flush();

$state_file = "./../../../../.storage/last_change.txt";

$connection_time = file_exists($state_file) ? (float)file_get_contents($state_file) : microtime(true);

$last_heartbeat = time();

while (true) {
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