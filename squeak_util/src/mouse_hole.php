<?php

use routes\Api_Routes;
use routes\Web_Routes;

require_once("./squeak_util/vendor/autoload.php");

spl_autoload_register(function ($class_name) {
    include ($class_name . ".php");
});

class mouse_hole {
    private $RUN = true;

    public $WEB_ROUTES;
    public $API_ROUTES;
    public $MIDDILEWARE_ROUTE_LOCAL_GROUPS;
    public $MIDDILEWARE_ROUTE_REGION;
    public $MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES;
    public $GLOBAL_MIDDLEWARE;
    public $REGIONAL_MIDDLEWARE;
    public $LOCAL_GROUP_MIDDLEWARE;

    public $logo;

    public $LINE_BREAK = "=======================================================================\n";
    public $LINE_LOWER = "_______________________________________________________________________\n";

    public function __construct($args) {
        if(sizeof($args) > 1) {
            $this->RUN = false;
        }
        else {
            $this->logo = file_get_contents("./squeak_util/src/resources/art/smol_mouse.txt");
        }
    }

    private function command_handler($command) {
        $command_prepped = strtolower($command);

        switch($command_prepped) {
            case "exit":
                $this->RUN = false;
                break;
            case "clear":
                $this->clear_screen();
                break;
            case "serve":
                $serve = new Serve();
                $serve->start();
                break;
            default:
                print("Command {$command} not found\n");
        }
    }

    private function single_exec($command, $options) {
        $command_prepped = strtolower($command);

        switch($command_prepped) {
            case "serve":
                print_r($options);
                break;
            default:
                print("Command {$command} is not valid\n");
        }
    }

    public function clear_screen() {
        system("clear");
        print_r($this->logo);
        print("Welcome to Squeak!\n");
        print("Type 'help' to get started\n");
        print("Type 'exit' to exit\n");
        print($this->LINE_BREAK);
    }

    public function load_routes() {
        if($this->server_files_check()) {
            include_once("./core/routes/Api_Routes.php");
            include_once("./core/routes/Web_Routes.php");
            $web_routes = new Web_Routes ();
            $this->WEB_ROUTES = $web_routes->ROUTES;
            $api_routes = new API_Routes ();
            $this->API_ROUTES = $api_routes->ROUTES;

            $web_routes = null;
            $api_routes = null;
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the squeak 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    private function server_files_check() {
        return file_exists("./core");
    }

    private function screen_render() {
        $history_file = ".squeak_history";
        if(file_exists($history_file)) {
            readline_read_history($history_file);
        }
        $this->clear_screen();

        while($this->RUN) {
            $command = readline("> ");
            readline_add_history($command);
            $this->command_handler($command);
        }
        readline_write_history($history_file);
        print("Goodbye!\n");;
    }

    public function menu($options, $text) {
        $selected = 0;
        system('stty -echo -icanon');
        system('tput civis');
        $this->menu_disp($options, $selected, $text);

        while (true) {
            $key = fread(STDIN, 1);
            if ($key === "\033") {
                fread(STDIN, 1);
                $key_sequence = fread(STDIN, 1);
                print($key_sequence);
                switch ($key_sequence) {
                    case "A":
                        $selected = max(0, $selected - 1);
                        break;
                    case "B":
                        $selected = min(count($options) - 1, $selected + 1);
                        break;
                }
                $this->menu_disp($options, $selected, $text);
            } else if ($key == "\n") {
                system('stty sane');
                system('tput cnorm');

                system('clear');
                return $options[$selected];
            }
        }
    }
    private function menu_disp($options, $selected, $text) {
        system('clear');

        if($selected > 0) {
            echo str_repeat(ANSI_CURSOR_UP, count($options));
        }
        print($text . ": \n");
        print($this->LINE_BREAK);

        foreach($options as $index => $option) {

            $option_padded = str_pad($option, 40);

            if($index == $selected) {
                echo ANSI_INVERSE . $option_padded . ANSI_RESET . "\n";
            }
            else {
                echo $option_padded . "\n";
            }
        }

    }

    public function find_env_files($file_list) {
        $env_file = ".env.";
        $out = [];
        foreach($file_list as $file) {
            if(str_starts_with($file, $env_file)) {
                $out[] = $file;
            }
        }

        return $out;
    }

    private function execute_as_command($command, $options) {
        $this->command_handler($command);
    }

    public function custom_entry($instructions) {
        system('clear');
        print($instructions . "\n");
        print($this->LINE_BREAK);
        $input = readline("> ");
        return $input;
    }

    public function env_types($file_list) {
        $whitelist = ["local", "dev", "test", "prod"];

        $env_file = ".env.";
        $out = [];
        foreach($file_list as $file) {
            $hold =  str_replace($env_file, "", $file);
            if(in_array($hold, $whitelist)) {
                $out[] = $hold;
            }
        }

        return $out;
    }

    public function error_txt($text) {
        print("\033[31m$text\033[0m\n");
    }

    public function success_txt($text) {
        print("\033[32m$text\033[0m\n");
    }

    public function warning_txt($text) {
        print("\033[33m$text\033[0m\n");
    }

    public function init($args) {
        if($this->RUN) {
            define('ANSI_RESET', "\033[0m");
            define('ANSI_INVERSE', "\033[7m");
            define('ANSI_CLEAR_LINE', "\033[2K");
            define('ANSI_CURSOR_UP', "\033[1A");
            $this->load_routes();
            $this->screen_render();
        }
        else {
            $this->single_exec($args[1], $args);
        }
    }
}