<?php

require_once realpath(__DIR__ . "/../vendor/autoload.php");

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/utils/' . str_replace("utils\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/utils/' . str_replace("utils\\", "", $className) . '.php');
    }
});

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/routes/' . str_replace("routes\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/routes/' . str_replace("routes\\", "", $className) . '.php');
    }
});

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/middleware/' . str_replace("middleware\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/middleware/' . str_replace("middleware\\", "", $className) . '.php');
    }
});

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/controllers/' . str_replace("controllers\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/controllers/' . str_replace("controllers\\", "", $className) . '.php');
    }
});

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/models/' . str_replace("models\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/models/' . str_replace("models\\", "", $className) . '.php');
    }
});

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/Page_Engine/' . str_replace("Page_Engine\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/Page_Engine/' . str_replace("Page_Engine\\", "", $className) . '.php');
    }
});

class Mouse_Core {
    public $APP_NAME;
    public $APP_VERSION;
    public $APP_VERSION_NAME;
    public $ADDRESS;
    public $PROTOCOL;
    public $ENVIRONMENT;
    public $SECRET;
    public $WEBSOCKET_ADDRESS;
    public $WEBSOCKET_KEY;
    public $WEBSOCKET_PROTOCOL;
    public $WEBSOCKET_PORT;
    public $FRONT_END_ADDRESS;
    public $FRONT_END_PORT;
    public $FRONT_END_PROTOCOL;
    public $TIME_BUFFER;
    public $RATE_LIMIT;
    public $WEB_ROUTES;
    public $API_ROUTES;
    public $REQ_TYPE;

    public $DB;
    public $SQLITE;

    public $DEV_MODE;

    private $REQUEST_DATA;

    public function __construct() {
        $this->bootstrap_env();
        $this->cors();
    }

    private function bootstrap_env() {
        $env_bootstrap = new \utils\Env_Bootstrap("app");
        $this->APP_NAME = $env_bootstrap->get_var("APP_NAME");
        $this->APP_VERSION = $env_bootstrap->get_var("APP_VERSION");
        $this->APP_VERSION_NAME = $env_bootstrap->get_var("APP_VERSION_NAME");
        $this->ADDRESS = $env_bootstrap->get_var("ADDRESS");
        $this->PROTOCOL = $env_bootstrap->get_var("PROTOCOL");
        $this->ENVIRONMENT = $env_bootstrap->get_var("ENVIRONMENT");
        $this->SECRET = $env_bootstrap->get_var("SECRET");
        $this->WEBSOCKET_ADDRESS = $env_bootstrap->get_var("WEBSOCKET_ADDRESS");
        $this->WEBSOCKET_KEY = $env_bootstrap->get_var("WEBSOCKET_KEY");
        $this->WEBSOCKET_PROTOCOL = $env_bootstrap->get_var("WEBSOCKET_PROTOCOL");
        $this->WEBSOCKET_PORT = $env_bootstrap->get_var("WEBSOCKET_PORT");
        $this->FRONT_END_ADDRESS = $env_bootstrap->get_var("FRONT_END_ADDRESS");
        $this->FRONT_END_PORT = $env_bootstrap->get_var("FRONT_END_PORT");
        $this->FRONT_END_PROTOCOL = $env_bootstrap->get_var("FRONT_END_PROTOCOL");
        $this->TIME_BUFFER = $env_bootstrap->get_var("TIME_BUFFER");
        $this->RATE_LIMIT = $env_bootstrap->get_var("RATE_LIMIT");
        $this->DEV_MODE = $env_bootstrap->get_var("DEV_MODE");

        $this->init_routes();
    }

    private function init_routes() {
        $web = new \routes\Web_Routes();
        $api = new \routes\Api_Routes();
        $this->WEB_ROUTES = $web->ROUTES;
        $this->API_ROUTES = $api->ROUTES;
    }

    private function bootstrap_db() {
        $this->DB = new \utils\Database_Handler();
        $this->SQLITE = new \utils\Sqlite_Handler();
    }

    private function cors() {
        $black_list = new \utils\Black_List();

        $front_end_address = $this->FRONT_END_PROTOCOL . "://" . $this->FRONT_END_ADDRESS . ":" . $this->FRONT_END_PORT;
        ob_start();
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] == $front_end_address) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    private function check_for_vars($routing_data, $uri_address) {
        $keys = array_keys($routing_data);
        $processed_addresses =  [false, []];
        $split_uri = explode("/", $uri_address);
        foreach ($keys as $key) {
            $split_key = explode("/", $key);
            if(sizeof($split_key) == sizeof($split_uri) && $key != "/") {
                $processed_addresses = $this->check_uri_match($split_key, $split_uri);
            }

            if($processed_addresses[0]) {
                $processed_addresses[] = $key;
                break;
            }
        }

        return $processed_addresses;
    }

    private function check_uri_match($split_key, $split_uri) {
        $var_out = [];
        $index = 0;
        foreach ($split_key as $element) {
            if(str_starts_with($element, ":") && $element !== ":" && str_ends_with($element, ":")) {

                $var_name = str_replace(":", "", $element);
                $var_out[$var_name] = $split_uri[$index];
                $index++;
            }
            else if($split_key[$index] == $element) {
                $index++;
            }
            else {
                return [false, []];
            }
        }

        return [true, $var_out];
    }

    private function web_routing($route) {
        $process_route = $this->routing($route);

        if(array_key_exists($process_route, $this->WEB_ROUTES)) {
            return ["route_data" => $this->WEB_ROUTES[$process_route], "vars" => []];
        }
        $check_out = $this->check_for_vars($this->WEB_ROUTES, $process_route);
        if($check_out[0]) {
            return ["route_data" => $this->WEB_ROUTES[$check_out[2]], "vars" => $check_out[1]];
        }

        $this->error_handle(["error" => "404", "message" => "Not Found"]);
    }

    private function api_routing($route) {
        $process_route = $this->routing($route);

        if(array_key_exists($process_route, $this->API_ROUTES)) {
            return ["route_data" => $this->API_ROUTES[$process_route], "vars" => []];
        }
        $check_out = $this->check_for_vars($this->API_ROUTES, $process_route);

        if($check_out[0]) {
            return ["route_data" => $this->API_ROUTES[$check_out[2]], "vars" => $check_out[1]];
        }

        $this->error_handle(["error" => "404", "message" => "Not Found"]);
    }

    private function routing($raw_route) {
        $route_split = explode("/", $raw_route);
        $out_process = "";

        foreach($route_split as $item) {
            if ($item !== "" && $item !== "api"){
                $out_process .= "/" . $item;
            }
        }

        if($out_process == "") {
            $out_process = "/";
        }

        return $out_process;
    }

    private function error_handle($error_data) {
        if(!array_key_exists("error", $error_data)) {
            foreach($error_data as $key => $error) {
                 if(array_key_exists("error", $error)) {
                     $error_out = $error;
                 }
             }
        }
        else {
            $error_out = $error_data;
        }

        if($this->REQ_TYPE == "web") {
            $page_engine = new \Page_Engine\Page_Engine();
            switch ($error_out["error"]) {
                case "404":
                    $page_engine->open_view("404", [], true);
                    die();
                    break;
                case "401":
                    $page_engine->open_view("401", [], true);
                    die();
                    break;
                case "403":
                    $page_engine->open_view("403", [], true);
                    die();
                    break;
                case "500":
                default:
                    $page_engine->open_view("500", [], true);
            }
        }

        else {
            $resp = new \utils\Response_Handler();
            switch ($error_out["error"]) {
                case "404":
                    $resp->send(["code" => "404", "api_message" => "Not Found"], "Generic", $this->REQUEST_DATA["request_tag"], $this->SECRET, false);
                    http_response_code(404);
                    die();
                case "401":
                    $resp->send(["code" => "401", "api_message" => "Access Denied"], "Generic", $this->REQUEST_DATA["request_tag"], $this->SECRET, false);
                    http_response_code(401);
                    die();
                case "403":
                    $resp->send(["code" => "403", "api_message" => "Forbidden"], "Generic", $this->REQUEST_DATA["request_tag"], $this->SECRET, false);
                    http_response_code(403);
                    die();
                case "400":
                    $resp->send(["code" => "400", "api_message" => "Problem with request"], "Generic", $this->REQUEST_DATA["request_tag"], $this->SECRET, false);
                    http_response_code(400);
                    die();
                default:
                    $resp->send(["code" => "418", "api_message" => "I'm a Mouse"], "Generic", $this->REQUEST_DATA["request_tag"], $this->SECRET, false);
                    http_response_code(418);
                    die();
            }
        }
    }

    private function run_middleware_pipeline($route_data, &$request_data, $vars) {
        $middleware_engine = new \middleware\Middleware_Engine($this->DB, $this->SQLITE);
        return $middleware_engine->run_middleware($route_data, $request_data, $vars);
    }

    private function load_routing() {
        $request = $_SERVER['REQUEST_URI'];

        $split_request = explode("/", $request);
        if(sizeof($split_request) > 1 &&$split_request[1] == "api") {
            $this->REQ_TYPE = "api";
            $out = $this->api_routing($request);
            $out["route_data"]["route"] = $request;
            return $out;
        }

        $this->REQ_TYPE = "web";
        if(sizeof($split_request) > 1) {
            $out =  $this->web_routing($request);
            $out["route_data"]["route"] = $request;
            return $out;
        }

        $out = $this->web_routing($request);
        $out["route_data"]["route"] = $request;
        return $out;
    }

    private function get_request_data() {
        try {
            $raw_data = file_get_contents('php://input');
            return json_decode($raw_data, true);
        }
        catch(Exception $e) {
            $this->error_handle(["error" => "400", "message" => "Problem with request"]);
        }
    }

    private function load_controller($routing_data, $request_data, $vars) {
        $controller_name = "controllers\\{$routing_data["class"]}";
        $request_data["vars"] = $vars;
        $request_data["server_secret"] = $this->SECRET;
        $controller = new $controller_name($this->DB, $this->SQLITE, $request_data);
        $method = $routing_data["method"];
        $controller->$method();
    }

    private function clean_up() {
        $this->DB = null;
        $this->SQLITE = null;
    }

    private function db_fail_tattle() {
        if(!$this->DB && $this->SQLITE) {
            $logger = new \utils\Log_Handler($this->SQLITE);
            $logger->log("Error", "Database connection failed", null);
        }
    }

    public function init() {
        $request_data = $this->get_request_data();
        $this->REQUEST_DATA = $request_data;
        $raw_routing_data = $this->load_routing();
        $routing_data = $raw_routing_data["route_data"];
        $vars = $raw_routing_data["vars"];
        try{
            $this->bootstrap_db();
            $this->db_fail_tattle();
            $middleware_output = $this->run_middleware_pipeline($routing_data, $request_data, $vars);
            if ($middleware_output) {
                $this->load_controller($routing_data, $request_data, $vars);
            } else {
                $this->error_handle($request_data['middleware_data']);
            }
        }
        catch(Exception $e) {
            $log_handler = new \utils\Log_Handler($this->SQLITE);
            $log_handler->log("Error", $e->getMessage() . "----" . $e->getTraceAsString(), null);
            $this->error_handle(["error" => "500", "message" => "Internal Server Error"]);
        }

        $this->clean_up();
    }
}
