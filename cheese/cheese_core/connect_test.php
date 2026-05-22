<?php
use OpenSwoole\Coroutine;
use OpenSwoole\Coroutine\Http\Client;
use Utils\EnvBootstrap;


class connect_test extends cheese_core {
    private $ADDRESS;
    private $PORT;


    public function run() {
        $openswoole_status = $this->openswoole_check();

        if($openswoole_status) {
            $vars = [
                "Address",
                "Port",
            ];
            $output = [];
            $env = $this->emberwhisk_and_file_chk();
            if($env) {
                include_once("Emberwhisk/src/Utils/EnvBootstrap.php");
                $env_load = new EnvBootstrap($env);
                $output["Address"] = $env_load->get_var("address");
                $output["Port"] = $env_load->get_var("port");
            }
            else {
                foreach ($vars as $var) {
                    system("clear");
                    print($this->LINE_BREAK);
                    print("Enter the value of {$var}\n");
                    print("Enter the 'abort' to cancel\n");
                    print($this->LINE_BREAK);
                    $val = readline("> ");
                    if (strtolower($val) == "abort") {
                        $output = [];
                        break;
                    } else {
                        $output[$var] = $val;
                    }
                }
            }
            if (sizeof($output) > 0) {
                $this->test($output['Address'], $output['Port']);
            } else {
                $this->clear_screen();
            }
        }
        else {
            $pecl = $this->pecl_check();
            $php_dev = $this->php_dev_check();
            if($pecl && $php_dev) {
                print("Installing openswoole...\n");
                $configs = 'enable-sockets="yes" enable-openssl="yes" enable-http2="yes" enable-mysqlnd="no" enable-hook-curl="no" enable-cares="no" with-postgres="no"';
                system("sudo pecl install -D '" . $configs ."' openswoole");
                $this->run_phpenmod();
                print($this->LINE_BREAK);
                print("Once OpenSwoole is installed you can rerun the test command.");
                print($this->LINE_BREAK);
            }
            if(!$pecl) {
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[31mYou are missing the PECL/PHP-PEAR package from your system please install it and retry the command.\n");
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[0m");
            }
            if(!$php_dev) {
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[31mYou are missing the PHP-Dev or equivalent package from your system please install it and retry the command.\n");
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[0m");
            }
        }
    }
    private function test($address, $port) {
        $this->ADDRESS = $address;
        $this->PORT = $port;
        co::run(function() {
            $client = new Client($this->ADDRESS, $this->PORT);

            $client->set(['timeout' => 5]);

            $is_upgrade = $client->upgrade('/');

            if ($is_upgrade) {
                $test_data = bin2hex(random_bytes(32));
                $payload = [
                    "user_id" => 0,
                    "message_type" => "bounce",
                    "data" => ["test_data" => $test_data],
                    "auth" => $test_data
                ];
                $message = json_encode($payload);
                $client->push($message);

                $response = $client->recv(5);

                if ($response) {
                    $response_data = json_decode($response->data, true);
                    if ($response_data['message_type'] == "bounce" && $response_data['data']['test_data'] == $test_data) {
                        print("\033[32m" . $this->LINE_BREAK);
                        print("\033[32mConnection Successful\n");
                        print("\033[32m" . $this->LINE_BREAK);
                        print("\033[0m");
                    }
                    else {
                        print("\033[31m" . $this->LINE_BREAK);
                        print("\033[31mBad Response From Server\n");
                        print("\033[31m" . $this->LINE_BREAK);
                        print("\033[0m");
                    }
                }
                else {
                    print("\033[31m" . $this->LINE_BREAK);
                    print("\033[31mServer is connected but\n");
                    print("\033[31mThere was No Response From the Server\n");
                    print("\033[31mMake sure that 'bounce' is in the route.\n");
                    print("\033[31m" . $this->LINE_BREAK);
                    print("\033[0m");
                }
            }
            else {
                print("\033[31m" . $this->LINE_BREAK);
                print("\033[31mFailed to connect to server\n");
                print("\033[31m" . $this->LINE_BREAK);
                print("\033[0m");
            }
        });
    }

    private function run_phpenmod() {
        if($this->phpenmod_check()) {
            $raw_routing = system("php --ini | grep php.ini");
            $route_array = explode("/", $raw_routing);
            array_shift($route_array);

            $output = "";
            foreach($route_array as $item) {
                if($item == "cli" || $item == "php.ini") {
                    break;
                }
                $output .= "/" . $item;
            }
            $final_route = $output . "/mods-available";
            system('sudo touch ' . $final_route . '/openswoole.ini');
            system('echo "; Configuration for Open Swoole' . "\n" . '; priority=30' . "\n" . 'extension=openswoole" | sudo tee ' . $final_route . '/openswoole.ini');
            system("sudo phpenmod -s cli openswoole");
            if($this->openswoole_check()) {
                print("Openswoole was installed successfully.\n");
            }
            else {
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[31mSomething went wrong while OpenSwoole was being installed\n");
                print("\033[31mPlease consider maually installing OpenSwoole using their Documentation: https://openswoole.com/docs/get-started/installation#enable-swoole-extension-in-php\n");
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[0m");
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mSystem doesn't have phpenmod on the system.\n");
            print("\033[31mUnable to add openswoole.so extension automatically please add the extension before trying to run your server!\n");
            print("\033[31m(Should be able to add the extension to your php.ini file which you can find by runnning the command 'php --ini | grep php.ini' in bash.)\n");
            print("\033[31mYou can check the OpenSwoole documentation at: https://openswoole.com/docs/get-started/installation#enable-swoole-extension-in-php for help.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    private function emberwhisk_and_file_chk() {
        if(!$this->server_files_check()) {
            return false;
        }

        $options = [
            "dev",
            "local",
            "test",
            "prod",
        ];

        $selected_env = $this->selection_menu($options, "Select what environment the server is running.");
        if(file_exists("Emberwhisk/src/.env.{$selected_env}")) {
            system('clear');
            return $selected_env;
        }
        return false;
    }
}