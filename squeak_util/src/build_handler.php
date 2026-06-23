<?php

class build_handler extends mouse_hole {
    public function bootstrap() {
        system("clear");
        $this->success_txt("Beginning build process...");
        print("Gettting Dependencies...\n\n");

        copy("https://getcomposer.org/installer", "composer-setup.php");
        system("php composer-setup.php");
        unlink('composer-setup.php');
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

    public function rollback() {
        if(!file_exists("./.dist_archive")) {
            $this->warning_txt("No previous dist snapshots found.");
            return;
        }

        $deploy_config = $this->get_deploy_config();
        if(!$deploy_config) {
            $this->clear_screen();
            return;
        }

        $snapshot_list = scandir("./.dist_archive");
        $snapshot_list = array_diff($snapshot_list, array('.', '..'));
        $snapshot_list = array_reverse($snapshot_list);
        $snapshot_date_list = [];
        $snapshot_keyed_list = [];


        foreach ($snapshot_list as $snapshot) {
            $time_stamp = filemtime("./.dist_archive/{$snapshot}");
            $time_out = date("Y-M-d H:i:s", $time_stamp);
            $snapshot_date_list[] = $time_out;
            $snapshot_keyed_list[$time_out] = $snapshot;
        }

        $snapshot_selected = $this->menu($snapshot_date_list, "Select the snapshot to rollback to", true);
        if($snapshot_selected == "Cancel") {
            $this->clear_screen();
            return;
        }

        $run_auth = $this->menu(["Yes", "No"], "Are you sure you want to rollback to snapshot {$snapshot_selected}? This will delete all existing changes in the Dist folder and push the distribution to the host.");

        if($run_auth == "No") {
            $this->clear_screen();
            $this->warning_txt("Rollback aborted.");
            return;
        }

        system("rm -r dist");
        system("tar -xJf .dist_archive/{$snapshot_keyed_list[$snapshot_selected]}");

        if(strtolower($deploy_config["deploy_to_host"]) == "true") {
            $this->deploy_to_host($deploy_config["deploy_to_host"], $deploy_config["host_folder_path"], $deploy_config["deploy_env_type"]);
        }

        $this->success_txt("Rollback to snapshot {$snapshot_selected} completed successfully!");
        readLine("Press enter to continue.");
        $this->clear_screen();
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
            case "DEPLOY_ENV_PATH":
                return "./core/.env.prod";
            case "DEPLOY_DB_CONFIG_PATH":
                return "./core/.env.db_config";
            case "DEPLOY_ENV_TYPE":
                return "prod";
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
            "DEPLOY_ENV_TYPE",
            "DEPLOY_DB_CONFIG_PATH",
        ];

        $file_content = "";
        foreach($file_lines as $line) {
            system("clear");
            print("Creating the Deployment config file\n");
            print("To abort type the command 'exit'\n");
            print($this->LINE_BREAK);
            if($line == "DEPLOY_TO_HOST_DIR") {
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

    public function deploy() {
        $deploy_config = $this->get_deploy_config();
        if(!$deploy_config) {
            $this->clear_screen();
            return;
        }
        $this->archive_dist();
        $this->archive_clean_up(intval($deploy_config["snap_shot_hold"]));
        $this->create_server_dist();
        if($this->get_env($deploy_config["deploy_env_path"], $deploy_config["deploy_env_type"], $deploy_config["deploy_db_config_path"])) {
            $this->success_txt("Deployment config files copied successfully!");

            $this->deploy_to_host($deploy_config["deploy_to_host"], $deploy_config["host_folder_path"], $deploy_config["deploy_env_type"]);
        }
        readLine("Press enter to continue.");
        $this->clear_screen();
    }

    private function archive_dist() {
        if(!file_exists("./dist")) {
            return;
        }
        if(!file_exists("./.dist_archive")) {
            mkdir("./.dist_archive");
        }

        $dist_name = "dist_" . date("Y-m-d_H-i-s");
        system("tar -cJf ./.dist_archive/{$dist_name}.tar.xz ./dist");
        $this->success_txt("Archive created successfully!");
        system("rm -r ./dist");
        $this->success_txt("Dist cleaned up successfully!");
    }

    public function archive_clean_up($snapshot_hold) {
        if(!file_exists("./.dist_archive")) {
            return;
        }
        $snapshot_list = scandir("./.dist_archive");
        $snapshot_list = array_diff($snapshot_list, array('.', '..'));

        $time_list = [];
        $keyed_list = [];

        if(sizeof($snapshot_list) > $snapshot_hold) {
            foreach($snapshot_list as $snapshot) {
                $time_stamp = filemtime("./.dist_archive/{$snapshot}");
                $time_list[] = $time_stamp;
                $keyed_list[$time_stamp] = $snapshot;
            }
        }

        rsort($time_list, SORT_NUMERIC);
        $latest = 0;

        foreach ($time_list as $time) {
            if($latest < $snapshot_hold) {
                 $latest++;
            }
            else {
                unlink("./.dist_archive/{$keyed_list[$time]}");
            }
        }

    }

    private function get_deploy_config() {
        while(true) {
            if (file_exists("./.deploy.env")) {
                $dotenv = \Dotenv\Dotenv::createImmutable("./", ".deploy.env");
                $dotenv->load();
                $deploy_to_host_dir = $_ENV["DEPLOY_TO_HOST_DIR"];
                $host_folder_path = $_ENV["HOST_FOLDER_PATH"];
                $snap_shot_hold = $_ENV["SNAP_SHOT_HOLD"];
                $deploy_env_path = $_ENV["DEPLOY_ENV_PATH"];
                $deploy_env_type = $_ENV["DEPLOY_ENV_TYPE"];
                $deploy_db_config_path = $_ENV["DEPLOY_DB_CONFIG_PATH"];

                $output = [
                    "deploy_to_host" => $deploy_to_host_dir,
                    "host_folder_path" => $host_folder_path,
                    "snap_shot_hold" => $snap_shot_hold,
                    "deploy_env_path" => $deploy_env_path,
                    "deploy_env_type" => $deploy_env_type,
                    "deploy_db_config_path" => $deploy_db_config_path,
                ];

                break;
            }
            else {
                $this->error_txt("Could not find the Deployment config file");
                readLine("Press enter to continue.");
                $answer = $this->menu(["Yes", "No"], "Would you like to create a Deployment config file?");

                if($answer == "Yes") {
                    $this->deploy_config();
                }
                else {
                    $output = false;
                    break;
                }
            }
        }

        return $output;
    }

    private function create_server_dist() {
        print("Creating server dist...\n");

        mkdir("./dist");
        system("cp -r ./core ./dist");
        system("cp -r ./public_html ./dist");
        system("cp -r ./vendor ./dist");

        $this->cleanup_config();
        $this->success_txt("Server dist created successfully!");
    }

    private function cleanup_config() {
        $raw_files = scanDir("./dist/core");
        foreach($raw_files as $file) {

            if(str_starts_with($file, ".env.")) {
                unlink("./dist/core/" . $file);
            }
        }
    }

    private function get_env($env_path, $env_type, $db_path) {
        if(file_exists($env_path)) {
            system("cp {$env_path} ./dist/core/.env.{$env_type}");
        }
        else {
            $this->error_txt("Could not find the environment file");
            return false;
        }

        if(file_exists($db_path)) {
            system("cp {$db_path} ./dist/core/.env.db_config");
        }
        else {
            $this->error_txt("Could not find the database config file");
            return false;
        }

        return true;
    }

    private function deploy_to_host($deploy_to_host, $host_folder_path, $env_type) {
        if(strtolower($deploy_to_host) == "false") {
            return;
        }
        $dotenv = \Dotenv\Dotenv::createImmutable("./dist/core", ".env.{$env_type}");
        $dotenv->load();
        $app_name = $_ENV["APP_NAME"];
        $new_deploy_path = $host_folder_path . $app_name;

        system("sudo cp dist -r {$new_deploy_path}-temp");
        if(file_exists($new_deploy_path)) {
            system("sudo rm -r {$new_deploy_path}-temp/public_html/resources/app_data");
            system("sudo cp -r {$new_deploy_path}/public_html/resources/app_data {$new_deploy_path}-temp/public_html/resources/app_data");
            system("sudo rm -r {$new_deploy_path}");
        }

        system("sudo mv {$new_deploy_path}-temp {$new_deploy_path}");
        system('sudo chown -R $USER:$USER ' . $new_deploy_path);
        system("sudo chmod -R 777 {$new_deploy_path}");
        $this->success_txt("Deployed to host successfully!");
    }
}