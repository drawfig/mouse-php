<?php
namespace middleware\modules;

class Replay_Check {
    private $DB;
    private $SQLITE;
    private $LOG;

    public function __construct($db, $sqlite) {
        $this->DB = $db;
        $this->LOG = new \utils\Log_Handler($sqlite);
        $this->SQLITE = $sqlite;
    }

    public function run($route_data, $request_data, $vars) {
        if($request_data["user_id"] === 0) {
            if($route_data["type"] == "GET") {
                return true;
            }
            $hash_gen = new \utils\Hash_Gen();
            $envelope = $request_data["user_id"] . $request_data["version"] . $request_data["request_tag"] . $request_data["seed"] . $request_data["timestamp"];

            $hash = $hash_gen->hmac_hash($request_data["data"], $envelope);

            if($hash === $request_data["auth"]) {
                return true;
            }
            return ["status" => false, "data" => ["error" => 403, "message" => "Replay detected"]];
        }

        return true;
    }
}