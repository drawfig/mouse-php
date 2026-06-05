<?php

class make_handler extends mouse_hole {
    public  function model() {
        system("clear");
        while (true) {
            $name = $this->custom_entry("Enter the name of the model (type: abort to exit): ");

            if(strtolower($name) == "abort") {
                $this->clear_screen();
                return;
            }

            $files = scandir("./core/models");

            $name = $name . "_Model";
            $name = $this->name_format($name);

            if ($name != "_Model" && !in_array($name . ".php", $files)) {
                print("Creating model...\n");
                $template = file_get_contents("./squeak_util/src/resources/templates/model_template.txt");
                $template = str_replace("{{MODEL_NAME}}", $name, $template);
                try {
                    file_put_contents("./core/models/" . $name . ".php", $template);
                    system("clear");
                    $this->success_txt("Model created successfully!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
                catch(Exception $e) {
                    system("clear");
                    $this->error_txt("Error creating model!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
            }
            elseif (in_array($name . ".php", $files)) {
                system("clear");
                $this->error_txt("Model already exists\n");
            }
            else {
                system("clear");
                $this->error_txt("Model name invalid\n");
            }
        }
    }

    public  function middleware() {
        system("clear");
        while (true) {
            $name = $this->custom_entry("Enter the name of the middleware module (type: abort to exit): ");

            if(strtolower($name) == "abort") {
                $this->clear_screen();
                return;
            }

            $files = scandir("./core/middleware/modules");

            $name = $this->name_format($name);

            if ($name != "" && !in_array($name . ".php", $files)) {
                print("Creating middleware module...\n");
                $template = file_get_contents("./squeak_util/src/resources/templates/middleware_template.txt");
                $template = str_replace("{{MIDDLEWARE_NAME}}", $name, $template);
                try {
                    file_put_contents("./core/middleware/modules/" . $name . ".php", $template);
                    system("clear");
                    $this->success_txt("Middleware module created successfully!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
                catch(Exception $e) {
                    system("clear");
                    $this->error_txt("Error creating middle module!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
            }
            elseif (in_array($name . ".php", $files)) {
                system("clear");
                $this->error_txt("Middleware module already exists\n");
            }
            else {
                system("clear");
                $this->error_txt("Middleware module name invalid\n");
            }
        }
    }

    public function controller() {
        system("clear");
        while (true) {
            $name = $this->custom_entry("Enter the name of the controller (type: abort to exit): ");

            if(strtolower($name) == "abort") {
                $this->clear_screen();
                return;
            }

            $files = scandir("./core/controllers");

            $name = $name . "_Controller";
            $name = $this->name_format($name);

            if ($name != "_Controller" && !in_array($name . ".php", $files)) {
                print("Creating controller...\n");
                $template = file_get_contents("./squeak_util/src/resources/templates/controller_template.txt");
                $template = str_replace("{{CONTROLLER_NAME}}", $name, $template);
                try {
                    file_put_contents("./core/controllers/" . $name . ".php", $template);
                    system("clear");
                    $this->success_txt("Controller created successfully!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
                catch(Exception $e) {
                    system("clear");
                    $this->error_txt("Error creating controller!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
            }
            elseif (in_array($name . ".php", $files)) {
                system("clear");
                $this->error_txt("Controller already exists\n");
            }
            else {
                system("clear");
                $this->error_txt("Controller name invalid\n");
            }
        }
    }

    public function view() {
        system("clear");
        while (true) {
            $name = $this->custom_entry("Enter the name of the View (type: abort to exit): ");

            if(strtolower($name) == "abort") {
                $this->clear_screen();
                return;
            }

            $files = scandir("./core/display_pages/views/");

            $name = $this->name_format($name);

            if ($name != "" && !in_array($name . ".php", $files)) {
                print("Creating view...\n");
                $template = file_get_contents("./squeak_util/src/resources/templates/view_template.txt");
                $template = str_replace("{{VIEW_NAME}}", $name, $template);
                try {
                    file_put_contents("./core/display_pages/views/" . $name . ".php", $template);
                    system("clear");
                    $this->success_txt("View created successfully!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
                catch(Exception $e) {
                    system("clear");
                    $this->error_txt("Error creating view!\n");
                    readline("Press enter to return to the main menu");
                    $this->clear_screen();
                    return;
                }
            }
            elseif (in_array($name . ".php", $files)) {
                system("clear");
                $this->error_txt("View already exists\n");
            }
            else {
                system("clear");
                $this->error_txt("View name invalid\n");
            }
        }
    }

    private function name_format($name) {
        $temp = explode(" ", $name);

        $holder = [];
        foreach($temp as $word) {
            $word = explode("_", $word);
            foreach($word as $item) {
                $holder[] = ucfirst($item);
            }
        }

        return implode("_", $holder);
    }
}