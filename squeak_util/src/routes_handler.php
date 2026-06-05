<?php

class routes_handler extends mouse_hole {
    public function show($web_routes, $api_routes) {
        $route_type = $this->menu(["Api", "Web", "Cancel"], "What type of routes do you want to see?");

        if($route_type == "cancel") {
            $this->clear_screen();
            return;
        }

        if($route_type == "Web") {
            $routes = $web_routes;
        }
        else {
            $routes = $api_routes;
        }

        if($routes) {
            $title_row = ["Route Name", "Route Controller", "Route Method", "Request Type", "Route Protection"];
            $table_rows = [];

            system('clear');
            print("$this->LINE_BREAK\n");
            print("                        Routing Layout Table     \n");
            print("$this->LINE_BREAK\n");
            foreach($routes as $route_key => $route_data) {
                $table_rows[] = [$route_key, $route_data["class"], $route_data["method"], $route_data["type"], $route_data["protected"]];
            }
            $this->make_table($title_row, $table_rows);
            print("$this->LINE_BREAK\n");
            print("\n");
        }
    }

    public function add($web_routes, $api_routes) {
        $route_type = $this->menu(["Api", "Web", "Cancel"], "What type of route do you want to add?");
        if($route_type == "cancel") {
            $this->clear_screen();
            return;
        }

        if($route_type == "Web") {
            $routes = $web_routes;
        }
        else {
            $routes = $api_routes;
        }

        if($routes) {
            system('clear');
            $new_route_data = $this->get_new_route_data($routes);
        }

    }

    private function get_files($type) {
        $exclude =[
            ".",
            "..",
        ];

        if($type == "controllers") {
            $source = "./core/controllers";
        }
        else {
            $source = "./core/modles";
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

    private function get_new_route_data($current_routes)
    {
        $class_list = $this->get_files("controllers");

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

        $selected_class = $this->menu($class_list, "What class should the route be handled by? ");
        if($selected_class == "Abort") {
            return false;
        }

        $route_data[] = $selected_class;

        $methods_list = $this->get_controller_methods($selected_class);

        var_dump($methods_list);
        /*$selected_method = $this->menu($methods_list, "What method should the route be handled by? ");
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
        */
    }

    public function get_controller_methods($classname) {
        $public_methods = $this->get_methods($classname);
        $names = [];

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
}