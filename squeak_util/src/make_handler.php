<?php

class make_handler extends mouse_hole {
    public function controller() {
        system("clear");
        while (true) {
            $name = $this->custom_entry("Enter the name of the controller (type: abort to exit): ");

            if(strtolower($name) == "abort") {
                $this->clear_screen();
                return;
            }

            $files = scandir("./core/controllers");

            var_dump($name);

            $name = $name . "_Controller";

            if ($name != "_Controller" && !in_array($name . ".php", $files)) {
                print("Creating controller...\n");
                $name = $this->name_format($name);
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
            elseif (in_array($name, $files)) {
                system("clear");
                $this->error_txt("Controller already exists\n");
            }
            else {
                system("clear");
                $this->error_txt("Controller name invalid\n");
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