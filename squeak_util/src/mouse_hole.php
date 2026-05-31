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

    public function __construct() {
        $this->logo = file_get_contents("./squeak_util/src/resources/art/smol_mouse.txt");
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

    public function init() {
        define('ANSI_RESET', "\033[0m");
        define('ANSI_INVERSE', "\033[7m");
        define('ANSI_CLEAR_LINE', "\033[2K");
        define('ANSI_CURSOR_UP', "\033[1A");
        $this->load_routes();
        $this->screen_render();
    }
}