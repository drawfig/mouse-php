<?php

class Serve extends mouse_hole {
    private $PID;
    private $WATCHER_PROCESS;

    public function test() {
        system("php squeak_util/src/resources/scripts/watcher.php");
    }

    public function start($full_mode = false) {
        $continue = $this->start_watcher();

        if(!$continue) {
            return;
        }

        if(extension_loaded("pcntl")) {
            pcntl_async_signals(true);

            pcntl_signal(SIGINT, [$this, 'shutdown_handler']);
            pcntl_signal(SIGTERM, [$this, 'shutdown_handler']);
        }
        else {
            print("⚠️  pcntl extension not loaded. Shutdown cleanup may not work perfectly.\n");
        }

        print("Starting php server...\n");
        Print("Press Ctrl+C to stop the server\n");

        passthru("PHP_ENV=dev php -d variables_order=E -S localhost:9000 -t public_html");

        $this->shutdown_handler();
    }

    private function start_watcher() {
        $watcher_script = "squeak_util/src/resources/scripts/watcher.php";

        $descriptors = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $this->WATCHER_PROCESS = proc_open(
            "php " . escapeshellarg($watcher_script),
            $descriptors,
            $pipes
        );

        if(is_resource($this->WATCHER_PROCESS)) {
            $status = proc_get_status($this->WATCHER_PROCESS);
            $this->PID = $status["pid"];
            print("✅ File watcher started (PIS: {$this->PID})\n");
            return true;
        }
        else {
            print("❌ Failed to start File watcher\n");
            return false;
        }
    }

    private function shutdown_handler() {
        print("Shutting down...\n");

        if(is_resource($this->WATCHER_PROCESS)) {
            proc_terminate($this->WATCHER_PROCESS, SIGTERM);
            usleep(500000);

            if(proc_get_status($this->WATCHER_PROCESS)["running"]) {
                proc_terminate($this->WATCHER_PROCESS, SIGKILL);
            }

            proc_close($this->WATCHER_PROCESS);
            print("✅ Watcher Stopped\n");
        }

        print("✅ Dev Server Stopped\n");
    }
}