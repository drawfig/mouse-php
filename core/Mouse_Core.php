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
    public $TIME_BUFFER;
    public $RATE_LIMIT;
    public $WEB_ROUTES;
    public $API_ROUTES;
    public $REQ_TYPE;

    public $DB;

    public $DEV_MODE;

    public function __construct() {
        $this->bootstrap_env();
        $this->cors();
        $this->bootstrap_db();
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
        $this->DB = new \utils\Mysql_Handler();
    }

    private function cors() {
        $black_list = new \utils\Black_List();

        ob_start();
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN']) && !in_array($_SERVER['HTTP_ORIGIN'], $black_list->LIST)) {
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

    private function web_routing($route) {
        $process_route = $this->routing($route);
        if(array_key_exists($process_route, $this->WEB_ROUTES)) {
            return $this->WEB_ROUTES[$process_route];
        }

        $this->error_handle(["error" => "404", "message" => "Not Found"]);
    }

    private function api_routing($route) {
        $process_route = $this->routing($route);
        if(array_key_exists($process_route, $this->API_ROUTES)) {
            return $this->API_ROUTES[$process_route];
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
            switch ($error_out["error"]) {
                case "404":
                    echo json_encode(["api_status" => false, "code" => "404", "api_message" => "Not Found"]);
                    http_response_code(404);
                    die();
                case "401":
                    echo json_encode(["api_status" => false, "code" => "401", "api_message" => "Access Denied"]);
                    http_response_code(401);
                    die();
                case "403":
                    echo json_encode(["api_status" => false, "code" => "403", "api_message" => "Forbidden"]);
                    http_response_code(403);
                    die();
                case "400":
                    echo json_encode(["api_status" => false, "code" => "400", "api_message" => "Problem with request"]);
                    http_response_code(400);
                    die();
                default:
                    echo json_encode(["api_status" => false, "code" => "418", "api_message" => "I'm a Mouse"]);
                    http_response_code(418);
                    die();
            }
        }
    }

    private function run_middleware_pipeline($route_data, $request_data) {
        $middleware_engine = new \middleware\Middleware_Engine($this->DB);
        return $middleware_engine->run_middleware($route_data, $request_data);
    }

    private function load_routing() {
        $request = $_SERVER['REQUEST_URI'];

        $split_request = explode("/", $request);
        if(sizeof($split_request) <= 1 &&$split_request[1] == "api") {
            $this->REQ_TYPE = "api";
            $out = $this->api_routing($request);
            $out["route"] = $request;
            return $out;
        }

        $this->REQ_TYPE = "web";
        if(sizeof($split_request) <= 1) {
            $out =  $this->web_routing($request);
            $out["route"] = $request;
            return $out;
        }

        $out = $this->web_routing($request);
        $out["route"] = $request;
        return $out;
    }

    private function middleware_2_routing($request_data, $middleware_data) {
        $out = $request_data;
        if(is_array($middleware_data)) {
            foreach($middleware_data as $key => $middleware) {
                $out[$key] = $middleware;
            }
        }

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

    private function load_controller($routing_data, $request_data) {
        $controller_name = "controllers\\{$routing_data["class"]}";
        $controller = new $controller_name($this->DB, $request_data);
        $method = $routing_data["method"];
        $controller->$method();
    }

    private function clean_up() {
        $this->DB = null;
    }

    public function init() {
        $routing_data = $this->load_routing();
        $request_data = $this->get_request_data();
        $middleware_output = $this->run_middleware_pipeline($routing_data, $request_data);
        if($middleware_output["status"]) {
            $request_data = $this->middleware_2_routing($request_data, $middleware_output["data"]);
            $this->load_controller($routing_data, $request_data);
        }
        else {
            $this->error_handle($middleware_output["data"]);
        }

        $this->clean_up();
    }
}