<?php

use middleware\Middleware_Module_Groups;
use middleware\Middleware_Route_Groups;
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
    public $MIDDILEWARE_ROUTE_GROUPS;
    public $MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES;
    public $GLOBAL_MIDDLEWARE;
    public $GROUP_MIDDLEWARE;

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

    private function command_handler($command, $options) {
        $command_prepped = strtolower($command);

        switch($command_prepped) {
            case "exit":
                $this->RUN = false;
                break;
            case "clear":
                $this->clear_screen();
                break;
            case "serve":
                $serve = new Serve($options);
                $serve->start();
                $serve = null;
                break;
            case "add-controller":
                $make = new make_handler($options);
                $make->controller();
                $make = null;
                break;
            case "add-model":
                $make = new make_handler($options);
                $make->model();
                $make = null;
                break;
            case "add-middleware":
                $make = new make_handler($options);
                $make->middleware();
                $make = null;
                break;
            case "add-view":
                $make = new make_handler($options);
                $make->view();
                $make = null;
                break;
            case "show-routes":
                $routes = new routes_handler($options);
                $routes->show($this->WEB_ROUTES, $this->API_ROUTES);
                $routes = null;
                break;
            case "add-route":
                $routes = new routes_handler($options);
                $output = $routes->add($this->WEB_ROUTES, $this->API_ROUTES);
                $routes = null;

                if($output) {
                    $this->WEB_ROUTES = $output["web_routes"];
                    $this->API_ROUTES = $output["api_routes"];
                }
                break;
            case "rmv-route":
                $routes = new routes_handler($options);
                $output = $routes->delete($this->WEB_ROUTES, $this->API_ROUTES);
                $routes = null;

                if($output) {
                    $this->WEB_ROUTES = $output["web_routes"];
                    $this->API_ROUTES = $output["api_routes"];
                }
                break;
            case "show-middleware":
                $middleware = new middleware_handler($options);
                $middleware->show();
                $middleware = null;
                break;
            case "mk-group":
                $middleware = new middleware_handler($options);
                $output = $middleware->make_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "rmv-group":
                $middleware = new middleware_handler($options);
                $output = $middleware->rmv_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "add-group-middleware":
                $middleware = new middleware_handler($options);
                $output = $middleware->add_to_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "rmv-group-middleware":
                $middleware = new middleware_handler($options);
                $output = $middleware->rmv_from_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "add-group-route":
                $middleware = new middleware_handler($options);
                $output = $middleware->add_route_to_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->WEB_ROUTES, $this->API_ROUTES);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                }
                break;
            case "rmv-group-route":
                $middleware = new middleware_handler($options);
                $output = $middleware->rmv_route_from_group($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                }
                break;
            case "add-global-middleware":
                $middleware = new middleware_handler($options);
                $output = $middleware->add_to_global($this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "rmv-global-middleware":
                $middleware = new middleware_handler($options);
                $output = $middleware->rmv_from_global($this->GLOBAL_MIDDLEWARE, $this->GROUP_MIDDLEWARE);
                $middleware = null;

                if($output) {
                    $this->GLOBAL_MIDDLEWARE = $output["global_middleware"];
                    $this->GROUP_MIDDLEWARE = $output["group_middleware"];
                }
                break;
            case "add-global-bypass":
                $middleware = new middleware_handler($options);
                $output = $middleware->add_to_bypass($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->API_ROUTES, $this->WEB_ROUTES);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                }
                break;
            case "rmv-global-bypass":
                $middleware = new middleware_handler($options);
                $output = $middleware->rmv_from_bypass($this->MIDDILEWARE_ROUTE_GROUPS, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                $middleware = null;

                if($output) {
                    $this->MIDDILEWARE_ROUTE_GROUPS = $output["groups"];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output["global_bypass"];
                }
                break;
            case "init":
                $build = new build_handler($options);
                $build->bootstrap();
                $build = null;
                break;
            case "gen-env":
                $build = new build_handler($options);
                $build->gen_env();
                $build = null;
                break;
            case "gen-db-config":
                $build = new build_handler($options);
                $build->gen_db_config();
                $build = null;
                break;
            case "add-auth":
                $build = new build_handler($options);
                $build->add_auth_scaffold();
                $build = null;
                break;
            case "gen-delopyment-config":
                $build = new build_handler($options);
                $build->deploy_config();
                $build = null;
                break;
            case "deploy":
                $build = new build_handler($options);
                $build->deploy();
                $build = null;
                break;
            case "rollback":
                $build = new build_handler($options);
                $build->rollback();
                $build = null;
                break;
            case "tail":
                $build = new Logging($options);
                $build->show();
                $build = null;
                break;
            case "help":
                $help = new help_handler($options);
                $help->help();
                $help = null;
                break;
            case "version":
                $help = new help_handler($options);
                $help->show_version();
                $help = null;
                break;
            default:
                print("Command {$command} not found\n");
        }
    }

    private function single_exec($command, $options) {
        $command_prepped = strtolower($command);

        switch($command_prepped) {
            case "serve":
                $serve = new Serve($options);
                $serve->exec($options);
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

    public function bootstrap_middleware() {
        include_once("./core/middleware/Middleware_Module_Groups.php");
        include_once("./core/middleware/Middleware_Route_Groups.php");

        $middleware_module_groups = new Middleware_Module_Groups();
        $middleware_route_groups = new Middleware_Route_Groups();

        $this->GLOBAL_MIDDLEWARE = $middleware_module_groups->GLOBAL_MIDDLEWARE;
        $this->GROUP_MIDDLEWARE = $middleware_module_groups->GROUP_MIDDLEWARE;
        $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $middleware_route_groups->GLOBAL_BYPASS_ROUTES;
        $this->MIDDILEWARE_ROUTE_GROUPS = $middleware_route_groups->GROUPS;
    }

    private function server_files_check() {
        return file_exists("./core");
    }

    private function screen_render($options) {
        $history_file = ".squeak_history";
        if(file_exists($history_file)) {
            readline_read_history($history_file);
        }
        $this->clear_screen();

        while($this->RUN) {
            $command = readline("> ");
            readline_add_history($command);
            $this->command_handler($command, $options);
        }
        readline_write_history($history_file);
        print("Goodbye!\n");;
    }

    public function true_false_display($text) {
        $options = [
            "false",
            "true",
        ];

        $output = $this->menu($options, $text, true);

        if($output == "Cancel") {
            return $output;
        }

        return filter_var($output, FILTER_VALIDATE_BOOLEAN); ;
    }

    public function menu($options, $text, $cancel = false) {
        $selected = 0;
        if($cancel) {
            $options[] = "Cancel";
        }
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

    public function make_table($title_row, $table_rows) {
        $col_length = [];
        foreach($title_row as $title) {
            $col_length[] = strlen($title);
        }

        foreach($table_rows as $row) {
            $pos = 0;
            foreach($row as $col) {
                if($col_length[$pos] < strlen($col)) {
                    $col_length[$pos] = strlen($col);
                }
                $pos++;
            }
        }

        $pos = 0;
        foreach ($col_length as $length) {
            if($length % 2 == 1) {
                $col_length[$pos] = $length++;
            }
            $pos++;
        }


        print($this->title_row_formating($col_length, $title_row));
        foreach($table_rows as $row) {
            print($this->table_row_formating($col_length, $row));
            print($this->gen_table_break($col_length));
        }

    }

    public function table_row_formating($sizes, $row) {
        $pos = 0;
        $line_out = "|   ";
        foreach($row as $col) {
            $length = $sizes[$pos];
            $line_out .= $this->column_spacing($length, $this->column_processing($col));
            if($pos < sizeof($row) - 1) {
                $line_out .= "   |   ";
            }
            $pos++;
        }
        return $line_out . "   |\n";
    }

    public function column_processing($col) {
        if(is_bool($col)) {
            if($col) {
                return "true";
            }
            else {
                return "false";
            }
        }

        return $col;

    }

    public function title_row_formating($sizes, $title_row) {
        $pos = 0;
        $line_out = "";
        foreach($title_row as $title) {
            $length = $sizes[$pos];
            $line_out .= $this->column_spacing($length, $title);
            if($pos < sizeof($title_row) - 1) {
                $line_out .= "   |   ";
            }
            $pos++;
        }
        return ANSI_INVERSE . "    " . $line_out . "    " . ANSI_RESET . "\n";
    }

    public function column_spacing($col_length, $text) {
        $text_length = strlen($text);

        if($text_length == $col_length) {
            return $text;
        }

        $diff = $col_length - $text_length;

        $back_space_count = floor($diff / 2);
        $front_space_count = $diff - $back_space_count;

        for($i = 0; $i < $front_space_count; $i++) {
            $text = " " . $text;
        }
        for($i = 0; $i < $back_space_count; $i++) {
            $text = $text . " ";
        }

        return $text;
    }

    public function gen_table_break($col_sizes ) {
        $line_out = "+---";
        $pos = 0;
        foreach($col_sizes as $size) {
            $line_out .= str_repeat("-", $size);
            if($pos < sizeof($col_sizes) - 1) {
                $line_out .= "---+---";
            }
            $pos++;
        }
        $line_out .= "---+\n";
        return $line_out;
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

    public function gen_random_str($length) {
        return bin2hex(random_bytes($length));
    }

    public function init($args) {
        if($this->RUN) {
            define('ANSI_RESET', "\033[0m");
            define('ANSI_INVERSE', "\033[7m");
            define('ANSI_CLEAR_LINE', "\033[2K");
            define('ANSI_CURSOR_UP', "\033[1A");
            $this->load_routes();
            $this->bootstrap_middleware();
            $this->screen_render($args);
        }
        else {
            $this->single_exec($args[1], $args);
        }
    }
}