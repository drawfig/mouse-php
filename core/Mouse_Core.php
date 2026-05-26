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
    include (realpath("./../core/models/") . "/" . $className . '.php');
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
    public $MYSQL_RUN;
    public $WEB_ROUTES;
    public $API_ROUTES;

    public $DB;

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
        $this->MYSQL_RUN = $env_bootstrap->get_var("MYSQL_RUN");

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

        return ["class" => "Error_Controller", "method" => "error_web_404", "protected" => false];
    }

    private function api_routing($route) {
        $process_route = $this->routing($route);
        if(array_key_exists($process_route, $this->API_ROUTES)) {
            return $this->API_ROUTES[$process_route];
        }

        return ["class" => "Error_Controller", "method" => "error_api_404", "protected" => false];
    }

    private function routing($raw_route) {
        $route_split = explode("/", $raw_route);
        $out_process = "";

        foreach($route_split as $item) {
            print($item . "\n");
            if ($item !== "" && $item !== "api"){
                $out_process .= "/" . $item;
            }
        }

        if($out_process == "") {
            $out_process = "/";
        }

        return $out_process;
    }

    private function run_middleware_pipeline($route_data) {
        $middleware_engine = new \middleware\Middleware_Engine();
        return $middleware_engine->run_middleware($route_data, $this->DB);
    }

    private function load_routing() {
        $request = $_SERVER['REQUEST_URI'];
        $split_request = explode("/", $request);
        if(sizeof($split_request) <= 1) {
            $out =  $this->web_routing($request);
            $out["route"] = $request;
            return $out;
        }

        if($split_request[1] == "api") {
            $out = $this->api_routing($request);
            $out["route"] = $request;
            return $out;
        }

        $out = $this->web_routing($request);
        $out["route"] = $request;
        return $out;
    }

    private function clean_up() {
        $this->DB = null;
    }

    public function init() {
        $routing_data = $this->load_routing();
        $middleware_output = $this->run_middleware_pipeline($routing_data);

        $this->clean_up();
    }
}