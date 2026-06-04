<?php

class Serve extends mouse_hole {
    private $PID;
    private $WATCHER_PROCESS;
    private $RUN_MODE;
    private $ADDRESS;

    private $PORT;

    private $RUN_TYPES;

    public function start() {
        $env_set = $this->set_environment();
        if(!$env_set) {
            return;
        }
        $this->address_set();
        $this->port_set();

        $this->run();
    }

    public function exec($options) {
        $core_scan = scandir("./core");
        $env_list = $this->find_env_files($core_scan);
        $this->RUN_TYPES = $this->env_types($env_list);

        if(sizeof($options) > 2) {
            foreach($options as $option) {
                $prep = explode("=", $option);
                switch($prep[0]) {
                    case "--address":
                    case "-a":
                        $this->ADDRESS = $prep[1] . ":";
                        break;
                    case "--port":
                    case "-p":
                        $this->PORT = $prep[1];
                        break;
                    case "--run-mode":
                    case "-r":
                        print_r($this->RUN_TYPES);
                        if(in_array($prep[1], $this->RUN_TYPES)) {
                            $this->RUN_MODE = $prep[1];
                        }
                        break;
                }
            }
        }

        if(!$this->ADDRESS) {
            $this->ADDRESS = "localhost:";
        }
        if(!$this->PORT) {
            $this->PORT = "9000";
        }
        if(!$this->RUN_MODE) {
            $this->set_environment();
        }

        $this->run();
    }

    private function run() {
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
            $this->warning_txt("⚠️  pcntl extension not loaded. Shutdown cleanup may not work perfectly.\n");
        }

        $this->success_txt("🐁 Starting mouse-php server...\n");
        print("Press Ctrl+C to stop the server\n");

        passthru("PHP_ENV={$this->RUN_MODE} DEV_MODE=true php -d auto_globals_jit=Off -q -S {$this->ADDRESS}{$this->PORT} -t public_html");

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
            $this->success_txt("✅ File watcher started (PIS: {$this->PID})\n");
            return true;
        }
        else {
            $this->error_txt("❌ Failed to start File watcher\n");
            return false;
        }
    }

    private function address_set() {
        $out = $this->menu(["localhost", "Host Mode", "Custom"], "How do you want the server to be hosted?");

        $instructions = "Please enter the custom address you want to host the server on (e.g. 0.0.0.0)";

        switch ($out) {
            case "localhost":
                $this->ADDRESS = "localhost:";
                break;
            case "Custom":
                system("clear");
                $this->ADDRESS =  $this->custom_entry($instructions) . ":";
                break;
            default:
                $this->ADDRESS = "0.0.0.0:";
        }
    }

    private function port_set() {
        $out = $this->menu(["9000", "80", "8080", "Custom"], "What port do you want to host the server on?");

        if($out == "Custom") {
            system("clear");
            $instructions = "Please enter the custom port you want to host the server on";
            $this->PORT = $this->custom_entry($instructions);
            return;
        }

        $this->PORT = $out;
    }


    private function set_environment() {
        $core_scan = scandir("./core");
        $env_list = $this->find_env_files($core_scan);
        $this->RUN_TYPES = $this->env_types($env_list);

        if(sizeof($this->RUN_TYPES) > 1) {
            $this->RUN_MODE = $this->menu($this->RUN_TYPES, "Select an available run type \n(If the type you are looking for is not listed, please run the gen-env command)");
        }
        else if(sizeof($this->RUN_TYPES) == 1) {
            $this->RUN_MODE = $this->RUN_TYPES[0];
            print("Running in the {$this->RUN_MODE} environment as it is the only available config, \nif you would like a different config please run the gen-env command.\n");
            readline("Press enter to continue...");
        }
        else {
            $this->error_txt("No available run types found. Please run the gen-env command\n");
            return false;
        }

        return true;
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
            $this->success_txt("✅ Watcher Stopped\n");
        }

        $this->success_txt("✅ Dev Server Stopped\n");
    }
}