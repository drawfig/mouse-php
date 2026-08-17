<?php
namespace middleware\modules;

class Authenticate {
    private $DB;
    private $LOG;

    public function __construct($db, $sqlite) {
        $this->DB = $db;
        $this->LOG = new \utils\Log_Handler($sqlite);
    }

    public function run($route_data, $request_data, $vars) {
        $user_data = $this->get_user_data($request_data["user_id"]);
        if(!$user_data) {
            $this->LOG->log("Error", "User not found", null);
            return ["status" => false, "data" => ["error" => 401, "message" => "User not found"]];
        }

        if(str_starts_with($route_data["address"], "/api" )) {
            return $this->api_auth_check($user_data, $request_data["data"], $request_data["seed"], $request_data["auth"]);
        }
        else {
            return $this->web_auth_check();
        }
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

        try {
            $user = $this->DB->make_query("select", $query, $val_array);

            if (sizeof($user) > 0) {
                return $user[0];
            }
            return false;
        }
        catch (\PDOException $e) {
            $this->LOG->log("Error", "Error getting user data: " . $e->getMessage(), null);
        }
    }

    private function api_auth_check(&$user_data, $post_data, $seed, $auth) {
        $hash_gen = new \utils\Hash_Gen();

        $token = $this->get_token($user_data["id"]);
        if(!$token) {
            return ["status" => false, "data" => ["error" => 401, "message" => "Unauthorized access"]];
        }
        $user_data["key"] = $token;
        $gen_hash = $hash_gen->hmac_hash($post_data, $seed, $token['token']);
        if(!$this->session_check($token)) {
            return ["status" => false, "data" => ["error" => 419, "message" => "Session expired"]];
        }


        if($gen_hash == $auth) {
            return ["status" => true, "data" => ["user" => $user_data]];
        }
        $this->LOG->log("Error", "Error 401: Unauthorized access", $user_data['id']);
        return ["status" => false, "data" => ["error" => 401, "message" => "Unauthorized access"]];
    }

    private function session_check($token) {
        $exp = $token["exp"];
        $current_time = (int) round(microtime(true) * 1000);
        if($current_time >= $exp) {
            return false;
        }
        return true;
    }

    private function web_auth_check() {
        if(isset($_SESSION['user'])) {
            return ["status" => true, "data" => ["user" => $_SESSION['user']]];
        }

        $page_engine = new \Page_Engine\Page_Engine();
        $this->LOG->log("Error", "Error 401: Unauthorized access", null);
        $page_engine->open_view("401", [], true);
        die();
    }

    private function get_token($user_id) {
        $query = "SELECT * FROM session_tokens WHERE user_id = :id";
        $val_array = [
            [
                "name" => ":id",
                "value" => $user_id,
                "type" => "i"
            ],
        ];

        try {
            $token = $this->DB->make_query("select", $query, $val_array);

            if($token && sizeof($token) > 0) {
                return $token[0]["token"];
            }
            return false;
        }
        catch (\PDOException $e) {
            $this->LOG->log("Error", "Error getting session Token: " . $e->getMessage(), null);
            return false;
        }
    }
}