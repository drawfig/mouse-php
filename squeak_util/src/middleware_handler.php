<?php

class middleware_handler extends mouse_hole {
    private $EXCLUDE = [
        ".",
        "..",
        "Middleware_Manager.php",
        "Middleware_Routing_Groups.php",
        "Middleware_Software_Groups.php",
    ];

    public function show() {
        system("clear");
        $files = $this->get_middleware_list();

        $this->make_table(["Middleware Name"], $files);
    }

    public function make_group($groups, $global_bypass, $global_middleware, $group_middleware) {
        print($this->LINE_BREAK);
        print("Creating a new Middleware Group.\n");
        print("To quit type the command 'abort'\n");
        print($this->LINE_BREAK);

        $group_name = readLine("Enter group name: ");

        if (array_key_exists($group_name, $groups)) {
            $this->warning_txt("Group already exists.");
            return false;
        }

        if (strlen($group_name) > 3) {
            $groups[$group_name] = [];
            $group_middleware[$group_name] = [];
        }
        else {
            $this->warning_txt("Group name must be longer than 3 characters.");
            return false;
        }

        $this->gen_middleware_module_groups($group_middleware, $global_middleware);
        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Middleware group created.");
        readLine("Press enter to continue...");
        $this->clear_screen();

        return ["groups" => $groups, "group_middleware" => $group_middleware, "global_bypass" => $global_bypass, "global_middleware" => $global_middleware];
    }

    public function rmv_group($groups, $global_bypass, $global_middleware, $group_middleware) {
        system("clear");
        $group_keys = array_keys($groups);
        $selected_group = $this->menu($group_keys, "Select middleware group to remove", true);

        if($selected_group == "Cancel") {
            $this->clear_screen();
            return false;
        }

        unset($groups[$selected_group]);
        unset($group_middleware[$selected_group]);
        $this->gen_middleware_module_groups($group_middleware, $global_middleware);
        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Middleware group removed.");
        readLine("Press enter to continue...");
        $this->clear_screen();

        return ["groups" => $groups, "group_middleware" => $group_middleware, "global_bypass" => $global_bypass, "global_middleware" => $global_middleware];
    }

    public function add_to_group($groups, $global_bypass, $global_middleware, $group_middleware) {
        system("clear");
        $group_keys = array_keys($groups);
        $selected_group = $this->menu($group_keys, "Select middleware group to add middleware to", true);
        if($selected_group == "Cancel") {
            $this->clear_screen();
            return false;
        }
        $modules = $this->get_middleware_list();
        $selected_module = $this->menu($modules, "Select middleware module to add to group", true);
        if($selected_module == "Cancel") {
            $this->clear_screen();
            return false;
        }

        if (!in_array($selected_module, $group_middleware[$selected_group])) {
            $group_middleware[$selected_group][] = $selected_module;
            $this->gen_middleware_module_groups($group_middleware, $global_middleware);
            $this->success_txt("Middleware added to group.");
            readLine("Press enter to continue...");
            $this->clear_screen();

            return ["groups" => $groups, "group_middleware" => $group_middleware, "global_bypass" => $global_bypass, "global_middleware" => $global_middleware];
        }
        else {
            $this->warning_txt("Middleware already exists in group.");
            readLine("Press enter to continue...");
            $this->clear_screen();
            return false;
        }
    }

    public function rmv_from_group($groups, $global_bypass, $global_middleware, $group_middleware) {
        system("clear");
        $group_keys = array_keys($groups);
        $selected_group = $this->menu($group_keys, "Select middleware group to remove middleware from", true);
        if($selected_group == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $which_module = $this->menu($group_middleware[$selected_group], "Select middleware module to remove from group", true);
        if($which_module == "Cancel") {
            $this->clear_screen();
        }

        $module_key = array_search($which_module, $group_middleware[$selected_group]);
        unset($group_middleware[$selected_group][$module_key]);
        $this->gen_middleware_module_groups($group_middleware, $global_middleware);
        $this->success_txt("Middleware removed from group.");
        readLine("Press enter to continue...");
        $this->clear_screen();
        return ["groups" => $groups, "group_middleware" => $group_middleware, "global_bypass" => $global_bypass, "global_middleware" => $global_middleware];
    }

    public function add_route_to_group($groups, $global_bypass, $web_routes, $api_routes) {
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

        $route_keys = array_keys($routes);
        $selected_route = $this->menu($route_keys, "Select route to add to group", true);
        if($selected_route == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $group_keys = array_keys($groups);
        $selected_group = $this->menu($group_keys, "Select middleware group to add route to", true);
        if($selected_group == "Cancel") {
            $this->clear_screen();
            return false;
        }

        if($route_type == "Api") {
            $selected_route = "/api" . $selected_route;
        }

        $groups[$selected_group][] = $selected_route;

        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Route added to group.");
        readLine("Press enter to continue...");
        $this->clear_screen();
        return ["groups" => $groups, "global_bypass" => $global_bypass];
    }

    public function rmv_route_from_group($groups, $global_bypass) {
        $group_keys = array_keys($groups);
        $selected_group = $this->menu($group_keys, "Select middleware group to remove route from", true);
        if($selected_group == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $route_keys = $groups[$selected_group];
        $selected_route = $this->menu($route_keys, "Select route to remove from group", true);
        if($selected_route == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $route_key = array_search($selected_route, $groups[$selected_group]);
        unset($groups[$selected_group][$route_key]);

        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Route removed from group.");
        readLine("Press enter to continue...");
        $this->clear_screen();
        return ["groups" => $groups, "global_bypass" => $global_bypass];
    }

    public function add_to_global($global_middleware, $group_middleware) {
        $modules_raw = $this->get_middleware_list();
        $modules = [];
        foreach ($modules_raw as $module) {
            if (!in_array($module, $global_middleware)) {
                $modules[] = $module;
            }
        }

        if (sizeof($modules) == 0) {
            $this->warning_txt("All middleware modules are already in global middleware.");
            readLine("Press enter to continue...");
            $this->clear_screen();
            return false;
        }
        $selected_module = $this->menu($modules, "Select middleware module to add to global middleware", true);
        if($selected_module == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $global_middleware[] = $selected_module;
        $this->gen_middleware_module_groups($group_middleware, $global_middleware);
        $this->success_txt("Middleware added to global middleware.");
        readLine("Press enter to continue...");
        $this->clear_screen();

        return ["group_middleware" => $group_middleware, "global_middleware" => $global_middleware];
    }

    public function rmv_from_global($global_middleware, $group_middleware) {
        $selected_module = $this->menu($global_middleware, "Select middleware module to remove from global middleware", true);
        if($selected_module == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $module_key = array_search($selected_module, $global_middleware);
        unset($global_middleware[$module_key]);
        $this->gen_middleware_module_groups($group_middleware, $global_middleware);
        $this->success_txt("Middleware removed from global middleware.");
        readLine("Press enter to continue...");
        $this->clear_screen();

        return ["global_middleware" => $global_middleware, "group_middleware" => $group_middleware];
    }

    public function add_to_bypass($groups, $global_bypass, $api_routes, $web_routes) {
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

        $route_keys = array_keys($routes);
        $keys_out = [];
        foreach($route_keys as $key) {
            if(!in_array($key, $global_bypass)) {
                if($route_type == "Api") {
                    $key = "/api" . $key;
                }
                $keys_out[] = $key;
            }
        }

        if(sizeof($keys_out) == 0) {
            $this->warning_txt("All routes are already in bypass.");
            readLine("Press enter to continue...");
            $this->clear_screen();
            return false;
        }
        $selected_route = $this->menu($keys_out, "Select route to add to bypass", true);
        if($selected_route == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $global_bypass[] = $selected_route;
        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Route added to bypass.");
        readLine("Press enter to continue...");
        $this->clear_screen();
        return ["global_bypass" => $global_bypass, "groups" => $groups];
    }

    public function rmv_from_bypass($groups, $global_bypass) {
        $selected_route = $this->menu($global_bypass, "Select route to remove from bypass", true);
        if($selected_route == "Cancel") {
            $this->clear_screen();
            return false;
        }

        $route_key = array_search($selected_route, $global_bypass);
        unset($global_bypass[$route_key]);
        $this->gen_middleware_route_groups($groups, $global_bypass);
        $this->success_txt("Route removed from bypass.");
        readLine("Press enter to continue...");

        $this->clear_screen();
        return ["global_bypass" => $global_bypass, "groups" => $groups];
    }

    private function gen_middleware_module_groups($groups_middleware, $global_middleware) {
        $template = file_get_contents("./squeak_util/src/resources/templates/Middleware_Module_Groups_Template.txt");
        $template = str_replace("{{GLOBAL_MIDDLEWARE}}", $this->list_to_string($global_middleware), $template);
        $template = str_replace("{{GROUP_MIDDLEWARE}}", $this->associative_array_to_string($groups_middleware), $template);

        file_put_contents("./core/middleware/Middleware_Module_Groups.php", $template);

    }

    private function gen_middleware_route_groups($groups, $global_bypass) {
        $template = file_get_contents("./squeak_util/src/resources/templates/Middleware_Route_Groups_Template.txt");
        $template = str_replace("{{GLOBAL_BYPASS_ROUTES}}", $this->list_to_string($global_bypass), $template);
        $template = str_replace("{{GROUPS}}", $this->associative_array_to_string($groups), $template);

        file_put_contents("./core/middleware/Middleware_Route_Groups.php", $template);
    }


    private function list_to_string($list, $space = 8) {
        $space_out = str_repeat(" ", $space);
        $out = [];
        foreach($list as $item) {
            $out[] = '"' . $item . '"';
        }
        return implode(",\n{$space_out}", $out);
    }

    private function associative_array_to_string($array) {
        $out = "";

        foreach($array as $key => $value) {
            if (sizeof($value) > 0) {
                $out .= '"' . $key . '"' . " => [\n            " . $this->list_to_string($value, 12) . "\n        ],";
            }
            else {
               $out .= '"' . $key . '"' . " => [],";
            }

            if($key !== array_key_last($array)) {
                $out .= "\n        ";
            }
        }

        return $out;
    }

    private function get_middleware_list() {
        $files = scandir("./core/middleware/modules");
        $out = [];
        foreach($files as $file) {
            if(str_ends_with($file, ".php")) {
                $out[] = str_replace(".php", "", $file);
            }
        }

        return $out;
    }
}