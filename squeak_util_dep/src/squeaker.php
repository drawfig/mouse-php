<?php

spl_autoload_register(function ($class_name) {
    include ($class_name . ".php");
});
class squeaker {
    private $RUN = true;

    public $ROUTES;
    public $MIDDILEWARE_ROUTE_LOCAL_GROUPS;
    public $MIDDILEWARE_ROUTE_REGION;
    public $MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES;
    public $GLOBAL_MIDDLEWARE;
    public $REGIONAL_MIDDLEWARE;
    public $LOCAL_GROUP_MIDDLEWARE;

    public $logo = " _____ _                         
/  __ \ |                        
| /  \/ |__   ___  ___  ___  ___ 
| |   | '_ \ / _ \/ _ \/ __|/ _ \
| \__/\ | | |  __/  __/\__ \  __/
 \____/_| |_|\___|\___||___/\___|
                                 
                                 \n";

    public $LINE_BREAK = "=======================================================================\n";
    public $LINE_LOWER = "_______________________________________________________________________\n";

    private function command_handler($command) {

        switch (strtolower($command)) {
            case "exit":
                $this->RUN = false;
                break;
            case "help":
                $load = new help_handler();
                $load->help_display();
                break;
            case "version":
                $load = new help_handler();
                $load->version_display();
                break;
            case "clear":
                $this->clear_screen();
                break;
            case "add-handler":
                $load = new make_handler();
                $load->make_handler();
                break;
            case "add-agent":
                $load = new make_handler();
                $load->make_agent();
                break;
            case "init":
                $load = new make_handler();
                $load->generate_server();
                break;
            case "gen-env":
                $load = new make_handler();
                $load->gen_env();
                break;
            case "connect-test":
                $load = new connect_test();
                $load->run();
                break;
            case "show-routes":
                $load = new management_handler();
                $load->show_routes($this->ROUTES);
                break;
            case "add-route":
                $load = new management_handler();
                $output = $load->create_route($this->ROUTES);
                if($output) {
                    $this->ROUTES = $output;
                }
                break;
            case "rmv-route":
                $load = new management_handler();
                $output = $load->delete_route($this->ROUTES);
                if($output) {
                    $this->ROUTES = $output;
                }
                break;
            case "show-middleware":
                $load = new middleware_handler();
                $load->show_middleware();
                break;
            case "add-middleware":
                $load = new middleware_handler();
                $load->create_middleware();
                break;
            case "rmv-middleware":
                $load = new middleware_handler();
                $output = $load->remove_middleware($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "add-middleware-group":
                $load = new middleware_handler();
                $output = $load->create_middleware_group($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->LOCAL_GROUP_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_REGION, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[1];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[2];
                    $this->REGIONAL_MIDDLEWARE = $output[3];
                    $this->GLOBAL_MIDDLEWARE = $output[4];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[5];
                }
                break;
            case "rmv-middleware-group":
                $load = new middleware_handler();
                $output = $load->remove_middleware_group($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->LOCAL_GROUP_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_REGION, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[1];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[2];
                    $this->REGIONAL_MIDDLEWARE = $output[3];
                    $this->GLOBAL_MIDDLEWARE = $output[4];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[5];
                }
                break;
            case "add-middleware-region":
                $load = new middleware_handler();
                $output = $load->create_middleware_region($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->LOCAL_GROUP_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_REGION, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[1];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[2];
                    $this->REGIONAL_MIDDLEWARE = $output[3];
                    $this->GLOBAL_MIDDLEWARE = $output[4];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[5];
                }
                break;
            case "rmv-middleware-region":
                $load = new middleware_handler();
                $output = $load->remove_middleware_region($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->LOCAL_GROUP_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_REGION, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[1];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[2];
                    $this->REGIONAL_MIDDLEWARE = $output[3];
                    $this->GLOBAL_MIDDLEWARE = $output[4];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[5];
                }
                break;
            case "add-global-middleware":
                $load = new middleware_handler();
                $output = $load->create_global_middleware($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "rmv-global-middleware":
                $load = new middleware_handler();
                $output = $load->remove_global_middleware($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "add-global-bypass":
                $load = new middleware_handler();
                $output = $load->create_middleware_bypass($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "rmv-global-bypass":
                $load = new middleware_handler();
                $output = $load->remove_global_bypass($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "add-route-to-group":
                $load = new middleware_handler();
                $output = $load->add_route_to_group($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "rmv-route-from-group":
                $load = new middleware_handler();
                $output = $load->remove_route_from_group($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "add-middleware-to-group":
                $load = new middleware_handler();
                $output = $load->add_middleware_to_group($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "rmv-middleware-from-group":
                $load = new middleware_handler();
                $output = $load->remove_middleware_from_group($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "add-route-to-region":
                $load = new middleware_handler();
                $output = $load->add_route_to_region($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES, $this->ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "rmv-route-from-region":
                $load = new middleware_handler();
                $output = $load->remove_route_from_region($this->MIDDILEWARE_ROUTE_LOCAL_GROUPS, $this->MIDDILEWARE_ROUTE_REGION, $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES);
                if($output) {
                    $this->MIDDILEWARE_ROUTE_LOCAL_GROUPS = $output[0];
                    $this->MIDDILEWARE_ROUTE_REGION = $output[1];
                    $this->MIDDILEWARE_ROUTE_GLOBAL_BYPASS_ROUTES = $output[2];
                }
                break;
            case "add-middleware-to-region":
                $load = new middleware_handler();
                $output = $load->add_middleware_to_region($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "rmv-middleware-from-region":
                $load = new middleware_handler();
                $output = $load->remove_middleware_from_region($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "add-group-to-region":
                $load = new middleware_handler();
                $output = $load->add_group_to_region($this->LOCAL_GROUP_MIDDLEWARE, $this->REGIONAL_MIDDLEWARE, $this->GLOBAL_MIDDLEWARE);
                if($output) {
                    $this->LOCAL_GROUP_MIDDLEWARE = $output[0];
                    $this->REGIONAL_MIDDLEWARE = $output[1];
                    $this->GLOBAL_MIDDLEWARE = $output[2];
                }
                break;
            case "run-logging":
                $load = new log_handler();
                $load->run_logging();
                break;
            default:
                print("Command {$command} not found\n");
        }
    }

    public function gen_random_str($length) {
        return bin2hex(random_bytes($length));
    }

    public function clear_screen() {
        system("clear");
        print($this->logo);
        print("Welcome to Cheese\n");
        print("Type 'help' to get started\n");
        print("Type 'exit' to exit\n");
        print($this->LINE_BREAK);
    }

    private function screen_render() {
        $history_file = ".cheese_history";
        if (file_exists($history_file)) {
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

    public function server_files_check() {
        return file_exists("Emberwhisk");
    }

    public function sqlite3_check() {
        $out = system("php -m | grep sqlite3");

        if($out == "") {
            return false;
        }
        else {
            return true;
        }
    }

    public function menu($options, $selected, $text) {
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

    public function pecl_check() {
        $pecl_check = system("pecl -V");
        if($pecl_check !== "") {
            return true;
        }
        else {
            return false;
        }
    }

    public function php_dev_check() {
        $dev_check = system("which phpize");
        if($dev_check !== "") {
            return true;
        }
        else {
            return false;
        }
    }

    public function openswoole_check() {
        $osw_check = system("php -m | grep openswoole");
        if($osw_check == "openswoole") {
            return true;
        }
        else {
            return false;
        }
    }

    public function phpenmod_check() {
        $mod_check = system("phpenmod");
        if($mod_check == "") {
            return false;
        }
        return true;
    }

    public function bool_to_str($bool) {
        if($bool) {
            return "true";
        }
        else {
            return "false";
        }
    }

    public function selection_menu($options, $text) {
        print("\e[?25l");
        $options[] = "Abort";
        $selected = 0;
        system('stty -echo -icanon');
        $this->menu($options, $selected, $text);

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
                $this->menu($options, $selected, $text);
            }
            else if($key == "\n") {
                system('stty sane');

                $output = $options[$selected];
                break;
            }
        }
        system('stty sane');
        print("\e[?25h");

        return $output;
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

    public function table_row_formating($sizes, $row)
    {
        $pos = 0;
        $line_out = "|   ";
        foreach ($row as $col) {
            $length = $sizes[$pos];
            $line_out .= $this->column_spacing($length, $this->column_processing($col));
            if ($pos < sizeof($row) - 1) {
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

    public function load_routes() {
        if($this->server_files_check()) {
            include_once("Emberwhisk/src/routes/Request_Routes.php");
            $request_routes = new Request_Routes();
            $this->ROUTES = $request_routes->REQUEST_ROUTES;
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
            return false;
        }
    }

    public function get_handler_methods($classname) {
       $public_methods = $this->get_methods($classname);

        foreach($public_methods as $method) {
            if($method !== "__construct") {
                $names[] = $method;
            }
        }

        return $names;
    }

    private function get_methods($classname) {
        $raw_out = system('php method_checker.php ' . $classname);
        system('clear');
        return json_decode($raw_out);
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