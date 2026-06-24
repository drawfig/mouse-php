<?php
namespace middleware;

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/modules/' . str_replace("middleware\\modules\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/modules/' . str_replace("middleware\\modules\\", "", $className) . '.php');
    }
});

class Middleware_Engine {
    private $ROUTE_GROUPS;
    private $GLOBAL_BYPASS_ROUTES;
    private $GROUP_MIDDLEWARE;
    private $GLOBAL_MIDDLEWARE;
    private $DB;
    private $SQLITE;

    public function __construct($db, $sqlite) {
        $middleware_routing = new \middleware\Middleware_Route_Groups();
        $middleware_modules = new \middleware\Middleware_Module_Groups();

        $this->ROUTE_GROUPS = $middleware_routing->GROUPS;
        $this->GLOBAL_BYPASS_ROUTES = $middleware_routing->GLOBAL_BYPASS_ROUTES;
        $this->GROUP_MIDDLEWARE = $middleware_modules->GROUP_MIDDLEWARE;
        $this->GLOBAL_MIDDLEWARE = $middleware_modules->GLOBAL_MIDDLEWARE;
        $this->DB = $db;
        $this->SQLITE = $sqlite;
    }

    public function run_middleware($route_data, $request_data) {
        $fuse = true;
        $middleware_list = $this->build_middleware_list($route_data);
        $data_out = [];

        foreach($middleware_list as $middleware) {
            if($fuse) {
                $middleware_class = "middleware\\modules\\{$middleware}";
                $middleware_instance = new $middleware_class($this->DB, $this->SQLITE);
                $output = $middleware_instance->run($route_data, $request_data);
            }
            else {
                break;
            }

            $processed_output = $this->middleware_output_handler($output);
            $fuse = $processed_output[0];
            if($processed_output[1]) {
                $data_out[$middleware] = $processed_output[1];
            }
        }

        return ["status" => $fuse, "data" => $data_out];
    }

    private function build_middleware_list($route_data)
    {
        $middleware_list = [];
        if(!array_key_exists($route_data['route'], $this->GLOBAL_BYPASS_ROUTES)) {
            $middleware_list = [...$middleware_list, ...$this->GLOBAL_MIDDLEWARE];
        }

        if(array_key_exists($route_data['route'], $this->ROUTE_GROUPS)) {
            $groups = $this->ROUTE_GROUPS[$route_data['route']];

            foreach($groups as $group) {
                if(array_key_exists($group, $this->GROUP_MIDDLEWARE)) {
                    $middleware_list = [...$middleware_list, ...$this->GROUP_MIDDLEWARE[$group]];
                }
            }
        }

        $middleware_out = [];

        foreach($middleware_list as $middleware) {
            if(!in_array($middleware, $middleware_out)) {
                $middleware_out[] = $middleware;
            }
        }

        return $middleware_out;
    }

    private function middleware_output_handler($middleware_output) {
        if(is_array($middleware_output) && array_key_exists("status", $middleware_output)) {
            if($middleware_output["status"]) {
                return [true, $middleware_output["data"]];
            }
            else {
                return [false, $middleware_output["data"]];
            }
        }
        else {
            return [$middleware_output, ""];
        }
    }
}