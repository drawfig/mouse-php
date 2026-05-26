<?php

class management_handler extends cheese_core {

    public function create_route($routes) {
        system('clear');
        if($routes) {
            $new_route_data = $this->get_new_route_data($routes);

            if($new_route_data) {
                $routes[$new_route_data[0]] = ["class" => $new_route_data[1], "method" => $new_route_data[2], "protected" => $new_route_data[3]];
                return $this->gen_routes_file($routes, "The Route has been added.\n");
            }
        }
        return false;
    }

    public function delete_route($routes) {
        if($routes) {
            while(true) {
                system('clear');
                print($this->LINE_BREAK);
                print("Creating a new route.\n");
                print("To quit type the command 'abort'\n");
                print($this->LINE_BREAK);
                $route_name = readline("What route do you want to delete? (example: 'bounce'): ");
                if($route_name == "abort") {
                    $this->clear_screen();
                    return false;
                }

                if(array_key_exists($route_name, $routes)) {
                    unset($routes[$route_name]);
                    return $this->gen_routes_file($routes, "The Route has been Removed.\n");
                }
                else {
                    print("The route {$route_name} does not exist.\n");
                    print("Please try again.\n");
                }
            }
        }
        return false;
    }

    public function show_routes($routes) {
        if($routes) {
            $title_row = ["Route Name", "Route Handler", "Route Method", "Route Protection"];
            $table_rows = [];

            system('clear');
            print("$this->LINE_BREAK\n");
            print("                        Routing Layout Table     \n");
            print("$this->LINE_BREAK\n");
            foreach($routes as $route_key => $route_data) {
                $table_rows[] = [$route_key, $route_data["class"], $route_data["method"], $route_data["protected"]];
            }
            $this->make_table($title_row, $table_rows);
            print("$this->LINE_BREAK\n");
            print("\n");
        }
    }

    private function get_new_route_data($current_routes)
    {
        $class_list = $this->get_files("handlers");

        $route_data = [];

        print($this->LINE_BREAK);
        print("Creating a new route.\n");
        print("To quit type the command 'abort'\n");
        print($this->LINE_BREAK);

        while (true) {
            $answer = readline("What should the route be called? (example: 'bounce'): ");
            if ($answer == "abort") {
                return false;
            }
            if (strlen($answer) > 2) {
                $route_data[] = $answer;
                break;
            } else {
                print("Answer must be longer than 3 characters.\n");
            }
        }

        $selected_class = $this->selection_menu($class_list, "What class should the route be handled by? ");
        if($selected_class == "Abort") {
            return false;
        }
        $route_data[] = $selected_class;

        $methods_list = $this->get_handler_methods($selected_class);
        $selected_method = $this->selection_menu($methods_list, "What method should the route be handled by? ");
        if($selected_method == "Abort") {
            return false;
        }
        $route_data[] = $selected_method;


        if (!array_key_exists($route_data[0], $current_routes)) {
            $protected_status = $this->true_false_display("Do you want this route to be protected?");

            if ($protected_status == "exit") {
                return false;
            }

            if ($protected_status == "true") {
                $route_data[] = true;
            }
            else {
                $route_data[] = false;
            }
            return $route_data;
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mThe {$route_data[0]} Route already exists.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
            return false;
        }
    }

    private function true_false_display($line)
    {
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

    private function gen_routes_file($routes, $end_mess) {
        $route_entries = "";
        foreach ($routes as $key => $route) {
            $route_entries .= "        '{$key}' => ['class' => '{$route['class']}', 'method' => '{$route['method']}', 'protected' => {$this->bool_to_str($route['protected'])}],\n";
        }

        $file_lines = '<?php
class Request_Routes {
    public $REQUEST_ROUTES = [' . "\n" .
    $route_entries . '
    ];
}';
        if(strlen($file_lines) > 0) {
            $file_address = "Emberwhisk/src/routes/Request_Routes.php";
            $file_create = fopen($file_address, "w");
            fwrite($file_create, $file_lines);
            print($end_mess);
            readLine("Press enter to continue.");
            $this->clear_screen();
            return $routes;
        }
        return false;
    }

    private function get_files($type) {
        $exclude =[
            ".",
            "..",
        ];

        if($type == "handlers") {
            $source = "Emberwhisk/src/Handlers";
        }
        else {
            $source = "Emberwhisk/src/Agents";
        }

        $raw_files = scandir($source);

        $output = [];
        foreach($raw_files as $raw_file) {
            if(!in_array($raw_file, $exclude)) {
                $output[] = str_replace(".php", "", $raw_file);
            }
        }

        return $output;
    }
}