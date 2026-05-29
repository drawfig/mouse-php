<?php

class start_handler extends squeaker {
    public function start_server() {
        $sqlChk = $this->sqlite3_check();
        if($this->server_files_check() && $sqlChk) {
            $options = [
                "dev",
                "local",
                "test",
                "prod",
                "cancel",
            ];

            $selected = 0;
            system('stty -echo -icanon');
            $this->menu($options, $selected, "Starting server in development mode.\nSelect an environment to start the server in");

            while (true) {
                $key = fread(STDIN, 1);
                if ($key === "\033") {
                    fread(STDIN, 1);
                    $key_sequence = fread(STDIN, 1);
                    switch ($key_sequence) {
                        case "A":
                            $selected = max(0, $selected - 1);
                            break;
                        case "B":
                            $selected = min(count($options) - 1, $selected + 1);
                            break;
                    }
                    $this->menu($options, $selected, "Starting server in development mode.\nSelect an environment to start the server in");
                } else if ($key == "\n") {
                    system('stty sane');

                    $env_type = $options[$selected];
                    break;
                }
            }

            system('stty sane');
            if ($env_type == "cancel") {
                $this->clear_screen();
                return;
            }

            $this->run_server($env_type);
        }
        else {
            print("\033[31m" . $this->LINE_BREAK);
            if(!$sqlChk) {
                print("\033[31mMissing Dependency:\n");
                print("\033[31mThe SQLite3 php module is missing please install it before trying to run the server\n");
            }
            if(!$this->server_files_check()) {
                print("\033[31mServer files missing:");
                print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            }
            print("\033[31m" . $this->LINE_BREAK);
            print("\033[0m");
        }
    }

    private function run_server($env_type) {
        system("clear");
        if($this->nodemon_check()) {
            system('cd Emberwhisk/src && nodemon --watch . --ext php --signal SIGTERM --exec "php run.php ' . $env_type .'"');
        }
        else if($this->npm_check()) {
            system("sudo npm install -g nodemon");
            system('cd Emberwhisk/src && nodemon --watch . --ext php --signal SIGTERM --exec "php run.php ' . $env_type .'"');
        }
        else {
            print("\033[31m" . $this->LINE_BREAK);
            print("\033[31mMissing dependency:");
            print("\033[31mNodemon and NPM is not installed.\n");
            print("\033[31mPlease check how to install NPM on your distro and rerun the wand 'start' command.\n");
            print("\033[31m" . $this->LINE_BREAK);
            print("\033[0m");
        }
    }

    private function nodemon_check() {
        if(system("nodemon --version") == "") {
            return false;
        }
        else {
            return true;
        }
    }

    private function npm_check() {
        if(system("npm --version") == "") {
            return false;
        }
        else {
            return true;
        }
    }
}