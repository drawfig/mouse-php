<?php

class make_handler extends cheese_core {

    public function make_agent() {
        if($this->server_files_check()) {
            $run = true;
            print($this->LINE_BREAK);
            print("Generate a new agent for the server.\n");
            print("Type 'cancel' to exit.\n");
            print($this->LINE_BREAK);
            while ($run) {
                $agent_name = readline("Agent Name: ");
                if (strtolower($agent_name) == "cancel") {
                    $this->clear_screen();
                    break;
                }

                if ($agent_name !== "") {
                    $status_chk = $this->generate_agent($agent_name);
                    print($this->LINE_BREAK);
                    print("Agent generated successfully.\n");
                    print($this->LINE_BREAK);
                }
                else {
                    print("Agent name cannot be empty.\n");
                }
                if ($status_chk) {
                    $run = false;
                }
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    public function make_handler() {
        if($this->server_files_check()) {
            $run = true;
            print($this->LINE_BREAK);
            print("Generate a new handler for the server.\n");
            print("Type 'cancel' to exit.\n");;
            print($this->LINE_BREAK);
            while ($run) {
                $handler_name = readline("Handler Name: ");
                if (strtolower($handler_name) == "cancel") {
                    $this->clear_screen();
                    break;
                }

                if ($handler_name !== "") {
                    $status_chk = $this->generate_handler($handler_name);
                    print($this->LINE_BREAK);
                    print("Handler generated successfully.\n");
                    print($this->LINE_BREAK);
                } else {
                    print("Handler name cannot be empty.\n");
                }
                if ($status_chk) {
                    $run = false;
                }
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    private function generate_agent($agent_name) {
        if($this->server_files_check()) {
            if (file_exists("Emberwhisk/src/Agents/{$agent_name}_agent.php")) {
                print("Agent already exists.\n");
                print("Please choose a different name.\n");
                return false;
            } else {
                print("Generating agent...\n");
                $file_content = '<?php
namespace Agents;
spl_autoload_register(function ($class_name) {
    if(file_exists(__DIR__ . "/Utils/" . str_replace("Utils\\\", "", $class_name) . ".php")) {
        require_once (__DIR__ . "/Utils/" . str_replace("Utils\\\", "", $class_name) . ".php");
    }
});

spl_autoload_register(function ($class_name) {
    include ($class_name . ".php");
});

class ' . $agent_name . '_agent {

}
';
                $file_create = fopen("Emberwhisk/src/Agents/{$agent_name}_agent.php", "w");
                fwrite($file_create, $file_content);
                return true;
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    private function generate_handler($handler_name) {
        if($this->server_files_check()) {
            if (file_exists("Emberwhisk/src/Handlers/{$handler_name}_handler.php")) {
                print("Handler already exists.\n");
                print("Please choose a different name.\n");
                return false;
            } else {
                print("Generating handler...\n");
                $file_content = '<?php
spl_autoload_register(function ($class_name) {
    if(file_exists(__DIR__ . "/Utils/" . str_replace("Utils\\\", "", $class_name) . ".php")) {
        require_once (__DIR__ . "/Utils/" . str_replace("Utils\\\", "", $class_name) . ".php");
    }
});

spl_autoload_register(function ($class_name) {
    if(file_exists(__DIR__ . "/Agents/" . str_replace("Agents\\\", "", $class_name) . ".php")) {
        require_once (__DIR__ . "/Agents/" . str_replace("Agents\\\", "", $class_name) . ".php");
    }
});

spl_autoload_register(function ($class_name) {
    include ($class_name . ".php");
});

class ' . $handler_name . '_handler {

    private $SECRET;
    private $DATA;
    private $FD;
    private $SERVER;
    private $DB;
    private $RUN_TYPE;

    public function __construct($secret, $data, $fd, $server, $db, $run_type) {
        $this->SECRET = $secret;
        $this->DATA = $data;
        $this->FD = $fd;
        $this->SERVER = $server;
        $this->DB = $db;
        $this->RUN_TYPE = $run_type;
    }

}';
                $file_create = fopen("Emberwhisk/src/Handlers/{$handler_name}_handler.php", "w");
                fwrite($file_create, $file_content);
                return true;
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    public function generate_server() {
            print ("Creation of Mouse-Php Project initialized...\n");
            print("Getting dependencies...\n");
            copy('https://getcomposer.org/installer', 'composer-setup.php');
            system("php composer-setup.php");
            unlink('composer-setup.php');
            system("php composer.phar install");
            $this->gen_env();
            print("Project Created!\n");
    }

    private function run_phpenmod() {
        if($this->phpenmod_check()) {
            $raw_routing = system("php --ini | grep php.ini");
            $route_array = explode("/", $raw_routing);
            array_shift($route_array);

            $output = "";
            foreach($route_array as $item) {
                if($item == "cli" || $item == "php.ini") {
                    break;
                }
                $output .= "/" . $item;
            }
            $final_route = $output . "/mods-available";
            system('sudo touch ' . $final_route . '/openswoole.ini');
            system('echo "; Configuration for Open Swoole' . "\n" . '; priority=30' . "\n" . 'extension=openswoole" | sudo tee ' . $final_route . '/openswoole.ini');
            system("sudo phpenmod -s cli openswoole");
            if($this->openswoole_check()) {
                print("Openswoole was installed successfully.\n");
            }
            else {
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[31mSomething went wrong while OpenSwoole was being installed\n");
                print("\033[31mPlease consider maually installing OpenSwoole using their Documentation: https://openswoole.com/docs/get-started/installation#enable-swoole-extension-in-php\n");
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[0m");
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mSystem doesn't have phpenmod on the system.\n");
            print("\033[31mUnable to add openswoole.so extension automatically please add the extension before trying to run your server!\n");
            print("\033[31m(Should be able to add the extension to your php.ini file which you can find by runnning the command 'php --ini | grep php.ini' in bash.)\n");
            print("\033[31mYou can check the OpenSwoole documentation at: https://openswoole.com/docs/get-started/installation#enable-swoole-extension-in-php for help.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }



    public function gen_env() {
        $options = [
            "dev",
            "local",
            "test",
            "prod",
            "database config"
        ];

        $selected = 0;
        system('stty -echo -icanon');
        $this->menu($options, $selected, "Select an environment config to generate");

        while(true) {
            $key = fread(STDIN,1);
            if($key === "\033") {
                fread(STDIN,1);
                $key_sequence = fread(STDIN,1);
                switch($key_sequence) {
                    case "A":
                        $selected = max(0, $selected - 1);
                        break;
                    case "B":
                        $selected = min(count($options) - 1, $selected + 1);
                        break;
                }
                $this->menu($options, $selected, "Select an environment config to generate");
            }
            else if($key == "\n") {
                system('stty sane');

                $env_type = $options[$selected];
                break;
            }
        }

        system('stty sane');
        if(file_exists("src/.env.{$env_type}")) {
            $rewrite = $this->yes_no_display("The .env.{$env_type} file already exists.\nDo you want to rewrite the file?");
            if($rewrite) {
                $this->make_env_file($env_type);
            }
            else {
                $this->clear_screen();
            }
        }
        else if($env_type == "database config") {
            if(file_exists("src/.env.db_config")) {
                $rewrite = $this->yes_no_display("The .env.db_config file already exists.\nDo you want to rewrite the file?");
                if($rewrite) {
                    $this->create_database_config();
                }
                else {
                    $this->clear_screen();
                }
            }
            else {
                $this->create_database_config();
            }
        }
        else {
            $this->make_env_file($env_type);
        }
    }

    private function yes_no_display($text) {
        $options = [
            "No",
            "Yes"
        ];

        $outOpts = [
            false,
            true
        ];

        $selected = 0;
        system('stty -echo -icanon');
        $this->menu($options, $selected, $text);

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
                $this->menu($options, $selected, $text);
            } else if ($key == "\n") {
                system('stty sane');

                return $outOpts[$selected];
            }
        }
    }

    private function make_env_file($env_type) {
        $file_lines = [
            "APP_NAME",
            "APP_VERSION",
            "APP_VERSION_NAME",
            "ADDRESS",
            "PROTOCOL",
            "ENVIRONMENT",
            "SECRET",
            "WEBSOCKET_ADDRESS",
            "WEBSOCKET_KEY",
            "WEBSOCKET_PROTOCOL",
            "WEBSOCKET_PORT",
            "WEBSOCKET_KEY",
            "FRONT_END_ADDRESS",
            "TIME_BUFFER",
            "RATE_LIMIT",
            "WORKER_COUNT",
            "MYSQL_RUN",
        ];

        $true_false = [
            "MYSQL_RUN"
        ];

        $selector_lines = [
        ];

        $deploy_lines = [

        ];
        $db_config_gen = false;

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the .env.{$env_type} file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            if(in_array($line, $true_false)) {
                $value = $this->true_false_display($line);
            }
            elseif (array_key_exists($line, $selector_lines)) {
                $options = $selector_lines[$line];
                $value = $this->selection_menu($options, "Select where the server should get the authorization data.");
            }
            else {
                $value = readline("Enter the value for {$line} (An empty value will result in the default value being used): ");
            }

            if($value == "exit" || $value == "Abort") {
                $file_content = "";
                break;
            }

            if($value == "") {
                $value = $this->default_getters($line, $env_type);
            }

            if($line == "MYSQL_RUN" && $value == "true") {
                $db_config_gen = true;
            }

            $file_content .= $line . '="' . $value . '"' . "\n";
        }
        if($file_content == "") {
            print("The .env.{$env_type} file was Aborted.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
            return;
        }

        if($env_type == "test" || $env_type == "prod") {
            foreach($deploy_lines as $line) {
                system("clear");
                print("Creating the .env.{$env_type} file\n");
                print("To abort type the command 'exit'\n");
                print($this->LINE_BREAK);
                if(in_array($line, $true_false)) {
                    $value = $this->true_false_display($line);
                }
                else {
                    $value = readline("Enter the value for {$line}: ");
                }

                if($value == "exit") {
                    $file_content = "";
                    break;
                }

                $file_content .= $line . '="' . $value . '"' . "\n";
            }
        }

        if($db_config_gen) {
            if(file_exists("Emberwhisk/src/.env.db_config")) {
                $rewrite = $this->yes_no_display("The .env.db_config file already exists.\nDo you want to rewrite the file?");
                if($rewrite) {
                    $this->create_database_config();
                }
                else {
                    $this->clear_screen();
                }
            }
            else {
                $this->create_database_config();
            }
        }

        if($file_content !== "") {
            $env_file = "Emberwhisk/src/.env.{$env_type}";
            $file_create = fopen($env_file, "w");
            fwrite($file_create, $file_content);
            print("The .env.{$env_type} file has been created.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
        }
        else {
            print("The .env.{$env_type} file was Aborted.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
        }
    }

    private function true_false_display($line) {
        $options = [
            "false",
            "true",
            "exit"
        ];

        $selected = 0;
        system('stty -echo -icanon');
        $this->menu($options, $selected, "Select the config value for {$line}");

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
                $this->menu($options, $selected, "Select the config value for {$line}");
            } else if ($key == "\n") {
                system('stty sane');

                return $options[$selected];
            }
        }
    }

    private function default_getters($line, $env_type) {
        switch ($env_type) {
            case "dev":
                $environment = "development";
                break;
            case "local":
                $environment = "local";
                break;
            case "test":
                $environment = "testing";
                break;
            case "prod":
                $environment = "production";
                break;
            default:
                $environment = "development";
        }

        switch($line) {
            case "APP_NAME":
                return "Mouse-Php";
            case "APP_VERSION":
            case "API_VERSION":
                return "1.0";
            case "APP_VERSION_NAME":
                return "Arrowhead";
            case "ADDRESS":
            case "WEBSOCKET_ADDRESS":
            case "FRONT_END_ADDRESS":
                return "127.0.0.1";
            case "WEBSOCKET_PORT":
                return "9502";
            case "PROTOCOL":
                return "http";
            case "ENVIRONMENT":
                return $environment;
            case "WEBSOCKET_PROTOCOL":
                return "ws";
            case "MYSQL_RUN":
                return "false";
            case "SECRET":
            case "WEBSOCKET_KEY":
                return $this->gen_random_str(32);
            case "DB_HOST":
                return "localhost";
            case "DB_PORT":
                return "3306";
            case "DB_USERNAME":
            case "DB_NAME":
                return "mouse-php";
            case "DB_PASSWORD":
                return "notsecure";
            case "TIME_BUFFER":
                return "1000";
            case "RATE_LIMIT":
                return "60";
            default:
                return "";
        }
    }

    private function create_database_config() {
        $file_lines = [
            "DB_HOST",
            "DB_PORT",
            "DB_NAME",
            "DB_USER",
            "DB_PASS",
        ];

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the .env.database config file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            $value = readline("Enter the value for {$line}: ");

            if ($value == "exit") {
                $file_content = "";
                break;
            }

            if ($value == "") {
                $value = $this->default_getters($line, "database config");;
            }


            $file_content .= $line . '="' . $value . '"' . "\n";
        }

            if($file_content !== "") {
                $env_file = "src/.env.db_config";
                $file_create = fopen($env_file, "w");
                fwrite($file_create, $file_content);
                print("The .env.db_config file has been created.\n");
                readLine("Press enter to continue.");
                $this->clear_screen();
            }
            else {
                print("The .env.db_config file was Aborted.\n");
                readLine("Press enter to continue.");
                $this->clear_screen();
            }
    }


}