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
        if($this->seed_check($request_data["user_id"], $request_data["seed"], $request_data["timestamp"], 300)) {
            return true;
        }

        return ["status" => false, "data" => ["error" => 403, "message" => "Replay detected"]];
    }

    private function seed_check($user_id, $seed, $timestamp, $window) {
        $ms_window = $window * 1000;
        $current_time = (int) round(microtime(true) * 1000);
        $this->seed_cleanup($current_time, $ms_window);

        if($current_time < ($timestamp - $ms_window) || $current_time > ($timestamp + $ms_window)) {
            return false;
        }

        $query = "SELECT * FROM seed_store WHERE user_id = :id AND seed = :seed";
        $val_array = [
            [
                "name" => ":id",
                "value" => $user_id,
                "type" => "i"
            ],
            [
                "name" => ":seed",
                "value" => $seed,
                "type" => "s"
            ]
        ];

        try {
            $seed_data = $this->SQLITE->make_query("select", $query, $val_array);
        }
        catch (\PDOException $e) {
            $this->LOG->log("Error", "Error checking seed: " . $e->getMessage(), null);
            return false;
        }

        if($seed_data && sizeof($seed_data) > 0) {
            return false;
        }

        $query = "INSERT INTO seed_store (user_id, seed, req_time) VALUES (:id, :seed, :time)";
        $val_array = [
            [
                "name" => ":id",
                "value" => $user_id,
                "type" => "i"
            ],
            [
                "name" => ":seed",
                "value" => $seed,
                "type" => "s"
            ],
            [
                "name" => ":time",
                "value" => $timestamp,
                "type" => "i"
            ]
        ];
        $this->SQLITE->make_query("insert", $query, $val_array);

        return true;
    }

    private function seed_cleanup($current_time, $window) {
        $shift = $window + $current_time + 60000;

        $query = "DELETE FROM seed_store WHERE req_time < :shift";
        $val_array = [
            [
                "name" => ":shift",
                "value" => $shift,
                "type" => "i"
            ],
        ];

        $this->SQLITE->make_query("delete", $query, $val_array);
    }
}