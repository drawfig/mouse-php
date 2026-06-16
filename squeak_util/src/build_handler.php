<?php

class build_handler extends mouse_hole {
    public function bootstrap() {
        system("clear");
        $this->success_txt("Beginning build process...");
        print("Gettting Dependencies...\n\n");

        copy("https://getcomposer.org/installer", "composer-setup.php");
        system("php composer-setup.php");
        system("php composer.phar install");
        $this->success_txt("Dependencies installed successfully!\n");
        print("Creating .env files...\n\n");
        readLine("Press enter to continue.");
        $this->gen_env();
        print("Creating DB Config...\n\n");
        readLine("Press enter to continue.");
        $this->gen_db_config();

        $run_auth = $this->menu(["Yes", "No"], "Would you like to add the standard Authentication Scaffold?");

        if($run_auth == "Yes") {
            $this->add_auth_scaffold();
        }

        $deploy_config = $this->menu(["Yes", "No"], "Would you like to create a Deployment config file?");
        if($deploy_config == "Yes") {
            $this->deploy_config();
        }
    }

    public function add_auth_scaffold() {
        system("clear");
        $this->success_txt("Adding Auth Scaffold...");
        $controller_temp = file_get_contents("./squeak_util/src/resources/templates/User_Controller_Temp.txt");
        $model_temp = file_get_contents("./squeak_util/src/resources/templates/User_Model_Temp.txt");

        file_put_contents("./core/controllers/User_Controller.php", $controller_temp);
        file_put_contents("./core/models/User_Model.php", $model_temp);

        $this->success_txt("Auth Scaffold added successfully!");
        print("Creating DB...\n\n");
        $this->build_db();

        readLine("Press enter to continue.");
        $this->clear_screen();
    }

    public function gen_env() {
        $options = [
            "dev",
            "local",
            "test",
            "prod"
        ];

        $selected = $this->menu($options, "Select the environment", true);
         if($selected == "Cancel") {
             $this->clear_screen();
             return false;
         }

         $this->make_env_file($selected);

         return true;
    }

    private function make_env_file($env_type) {
        $file_lines = [
            "APP_NAME",
            "APP_VERSION",
            "APP_VERSION_NAME",
            "ADDRESS",
            "PROTOCOL",
            "ENVIRONMENT",
            "SECRET",
            "WEBSOCKET_ADDRESS",
            "WEBSOCKET_KEY",
            "WEBSOCKET_PROTOCOL",
            "WEBSOCKET_PORT",
            "FRONT_END_ADDRESS",
            "PEPPER",
            "TIME_BUFFER",
            "RATE_LIMIT",
        ];

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the .env.{$env_type} file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            $value = readline("Enter the value for {$line} (An empty vlaue will result in the default value being used): ");

            if(strtolower($value) == "exit") {
                $file_content = "";
                break;
            }

            if($value == "") {
                $value = $this->default_getters($line, $env_type);
            }

            $file_content .= $line . '="' . $value . '"' . "\n";
        }
        if($file_content == "") {
            print("The .env.{$env_type} file was Aborted.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
            return;
        }

        $env_file = "core/.env.{$env_type}";
        $file_create = fopen($env_file, "w");
        fwrite($file_create, $file_content);
        $this->success_txt("The .env.{$env_type} file has been created.");
        readLine("Press enter to continue.");
        $this->clear_screen();
    }

    public function gen_db_config() {
        $file_lines = [
            "DB_HOST",
            "DB_PORT",
            "DB_NAME",
            "DB_USERNAME",
            "DB_PASSWORD",
        ];

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the database config file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            $value = readline("Enter the value for {$line} (An empty value will result in the default value being used): ");

            if(strtolower($value) == "exit") {
                $file_content = "";
                break;
            }

            if($value == "") {
                $value = $this->default_getters($line, "database config");
            }

            $file_content .= $line . '="' . $value . '"' . "\n";
        }

        if($file_content == "") {
            print("The database config file was Aborted.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
            return;
        }

        $env_file = "core/.env.db_config";
        $file_create = fopen($env_file, "w");
        fwrite($file_create, $file_content);
        $this->success_txt("The database config file has been created.");
        readLine("Press enter to continue.");
        $this->clear_screen();
    }

    private function default_getters($line, $env_type) {
        switch ($env_type) {
            case "dev":
                $environment = "development";
                break;
            case "local":
                $environment = "local";
                break;
            case "test":
                $environment = "testing";
                break;
            case "prod":
                $environment = "production";
                break;
            default:
                $environment = "development";
        }

        switch($line) {
            case "APP_NAME":
                return "Mouse-Php";
            case "APP_VERSION":
            case "API_VERSION":
                return "1.0";
            case "APP_VERSION_NAME":
                return "Arrowhead";
            case "ADDRESS":
            case "WEBSOCKET_ADDRESS":
            case "FRONT_END_ADDRESS":
                return "127.0.0.1";
            case "WEBSOCKET_PORT":
                return "9502";
            case "PROTOCOL":
                return "http";
            case "ENVIRONMENT":
                return $environment;
            case "WEBSOCKET_PROTOCOL":
                return "ws";
            case "MYSQL_RUN":
                return "false";
            case "SECRET":
            case "WEBSOCKET_KEY":
                return $this->gen_random_str(32);
            case "DB_HOST":
                return "localhost";
            case "DB_PORT":
                return "3306";
            case "DB_USERNAME":
            case "DB_NAME":
                return "mouse-php";
            case "DB_PASSWORD":
                return "notsecure";
            case "TIME_BUFFER":
                return "1000";
            case "RATE_LIMIT":
                return "60";
            case "HOST_FOLDER_PATH":
                return "/var/www/";
            case "SNAP_SHOT_HOLD":
                return "15";
            default:
                return "";
        }
    }

    private function build_db() {
        $db_config = $this->get_db_config();
        if(!$db_config) {
            $this->error_txt("Could not find the database config file");
            readLine("Press enter to continue.");
            $this->gen_db_config();
            $db_config = $this->get_db_config();
        }
        $db_type = $this->db_find();
        if($db_type && $db_config) {
            system("cd squeak_util/src/resources/templates && {$db_type} -u {$db_config["user"]} -p {$db_config["pass"]} {$db_config["name"]} < db_template.sql");
            $this->success_txt("Database created successfully!");
            return true;
        }

        if(!$db_type) {
            $this->error_txt("MariaDB or MySQL not found. Please install one of these and try again.");
        }
        return false;
    }

    private function get_db_config() {
        if(file_exists("./core/.env.db_config")) {
            $dotenv = \Dotenv\Dotenv::createImmutable("./core", ".env.db_config");
            $dotenv->load();
            $db_host = $_ENV["DB_HOST"];
            $db_port = $_ENV["DB_PORT"];
            $db_name = $_ENV["DB_NAME"];
            $db_user = $_ENV["DB_USERNAME"];
            $db_pass = $_ENV["DB_PASSWORD"];

            return ["host" => $db_host, "port" => $db_port, "name" => $db_name, "user" => $db_user, "pass" => $db_pass];
        }

        return false;
    }

    private function db_find() {
        $maria_chk =  system("which mariadb");
        if($maria_chk) {
            return "mariadb";
        }
        $mysql_chk =  system("which mysql");
        if ($mysql_chk) {
            return "mysql";
        }
        return false;
    }

    public function deploy_config() {
        $file_lines = [
            "DEPLOY_TO_HOST_DIR",
            "HOST_FOLDER_PATH",
            "SNAP_SHOT_HOLD",
            "DEPLOY_ENV_PATH",
            "DEPLOY_DB_CONFIG_PATH",
        ];

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the Deployment config file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            if($line == "DEPLOY_TO_HOST_FOLDER") {
                $value = $this->menu(["True", "False"], "Would you like to deploy to a host folder?", true);
            }
            else {
                $value = readline("Enter the value for {$line} (An empty value will result in the default value being used): ");
            }

            if(strtolower($value) == "exit" || strtolower($value) == "cancel") {
                $file_content = "";
                break;
            }

            if($value == "") {
                $value = $this->default_getters($line, "deploy config");
            }

            $file_content .= $line . '="' . $value . '"' . "\n";
        }

        if($file_content == "") {
            print("The Deployment config file was Aborted.\n");
            readLine("Press enter to continue.");
            $this->clear_screen();
            return;
        }

        $env_file = ".deploy.env";
        $file_create = fopen($env_file, "w");
        fwrite($file_create, $file_content);
        $this->success_txt("The Deployment config file has been created.");
        readLine("Press enter to continue.");
        $this->clear_screen();
    }
}