<?php
namespace middleware\modules;

class Authenticate {
    private $DB;
    private $LOG;

    public function __construct($db, $sqlite) {
        $this->DB = $db;
        $this->LOG = new \utils\Log_Handler($sqlite);
    }

    public function run($route_data, $request_data) {
        if($route_data["protected"]) {
            $user_data = $this->get_user_data($request_data["user_id"]);
            if(!$user_data) {
                $this->LOG->log("Error", "User not found", null);
                return ["status" => false, "data" => ["error" => 1, "message" => "User not found"]];
            }

            if(str_starts_with($route_data["address"], "/api" )) {
                return $this->api_auth_check($user_data, $request_data["data"], $request_data["auth"]);
            }
            else {
                return $this->web_auth_check();
            }

        }
        return true;
    }

    private function get_user_data($user_id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $val_array = [
            [
                "name" => ":id",
                "value" => $user_id,
                "type" => "i"
            ],
        ];

        $user = $this->DB->make_query("select", $query, $val_array);

        if(sizeof($user) > 0) {
            return $user[0];
        }
        return false;
    }

    private function api_auth_check($user_data, $post_data, $auth) {
        $gen_hash = hash("sha256", $user_data["key"] . json_encode($post_data));

        if($gen_hash == $auth) {
            return true;
        }
        $this->LOG->log("Error", "Error 401: Unauthorized access", $user_data['id']);
        return ["status" => false, "data" => ["error" => 401, "message" => "Unauthorized access"]];
    }

    private function web_auth_check() {
        if(isset($_SESSION['user'])) {
            return true;
        }

        $page_engine = new \Page_Engine\Page_Engine();
        $this->LOG->log("Error", "Error 401: Unauthorized access", null);
        $page_engine->open_view("401", [], true);
        die();
    }
}