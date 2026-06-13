<?php

class routes_handler extends mouse_hole {
    public function show($web_routes, $api_routes) {
        $route_type = $this->menu(["Api", "Web"], "What type of routes do you want to see?", true);

        if($route_type == "Cancel") {
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
        $route_type = $this->menu(["Api", "Web"], "What type of route do you want to add?", true);
        if($route_type == "Cancel") {
            $this->clear_screen();
            return false;
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
            if($new_route_data) {
                $address = $new_route_data["address"];
                unset($new_route_data["address"]);
                $routes[$address] = $new_route_data;
                $out = $this->gen_routes_file($routes, $route_type);
                if($route_type == "Web") {
                    return ["web_routes" => $out, "api_routes" => $api_routes];
                }
                else {
                    return ["web_routes" => $web_routes, "api_routes" => $out];
                }
            }
        }

        return false;

    }

    public function delete($web_routes, $api_routes) {
        $route_type = $this->menu(["Api", "Web"], "What type of route do you want to add?", true);
        if($route_type == "Cancel") {
            $this->clear_screen();
            return false;
        }

        if($route_type == "Web") {
            $routes = $web_routes;
        }
        else {
            $routes = $api_routes;
        }

        if($routes) {
            system('clear');
            $delete_route = $this->menu(array_keys($routes), "What route do you want to delete?", true);
            if($delete_route == "Cancel") {
                $this->clear_screen();
                return false;
            }
            unset($routes[$delete_route]);
            $out = $this->gen_routes_file($routes, $route_type);
            if($route_type == "Web") {
                return ["web_routes" => $out, "api_routes" => $api_routes];
            }
            else {
                return ["web_routes" => $web_routes, "api_routes" => $out];
            }
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
            $answer = readline("What should the route be the Route Address? (example: 'test' for '/test'): ");
            if ($answer == "abort") {
                return false;
            }
            if (strlen($answer) > 2) {
                $route_data["address"] = "/" . $answer;
                break;
            } else {
                print("Answer must be longer than 3 characters.\n");
            }
        }

        if (!array_key_exists($route_data["address"], $current_routes)) {
            $selected_class = $this->menu($class_list, "What class should the route be handled by?", true);
            if($selected_class == "Cancel") {
                return false;
            }

            $route_data["class"] = $selected_class;

            $methods_list = $this->get_controller_methods($selected_class);

            $selected_method = $this->menu($methods_list, "What method should the route be handled by? ", true);
            if($selected_method == "Cancel") {
                return false;
            }

            $route_data["method"] = $selected_method;

            $req_type = $this->menu(["GET", "POST", "PUT", "PATCH", "DELETE"], "What type of request should the route handle?", true);

            $route_data["type"] = $req_type;


            $protected_status = $this->true_false_display("Do you want this route to be protected?");

            if ($protected_status === "Cancel") {
                return false;
            }

            $route_data["protected"] = $protected_status;

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

    private function gen_routes_file($routes, $type) {
        $template = file_get_contents("./squeak_util/src/resources/templates/route_template.txt");
        $template = str_replace("{{ROUTE_LIST}}", $this->list_format($routes), $template);
        $template = str_replace("{{ROUTE_TYPE}}", $type, $template);

        file_put_contents("./core/routes/" . $type . "_Routes.php", $template);
        system("clear");
        $this->success_txt("Route file updated successfully!\n");
        readline("Press enter to return to the main menu");
        $this->clear_screen();
        return $routes;
    }

    private function list_format($list) {
        $output = "";

        foreach($list as $key => $item) {
            $bool_out = $item["protected"] ? "true" : "false";

            $output .= "'{$key}' => ['class' => '{$item["class"]}', 'method' => '{$item["method"]}', 'type' => '{$item["type"]}', 'protected' => {$bool_out}],\n        ";
        }

        return $output;
    }
}