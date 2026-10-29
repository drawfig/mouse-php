<?php
namespace middleware\modules;

class Seed_Check {
    private $CLEAN_RATE = 5;
    private $TIMEOUT = 11;
    private $WINDOW = 300;

    private $DB;
    private $SQLITE;

    public function __construct($db, $sqlite) {
        $this->SQLITE = $sqlite;
    }

    public function run($route_data, $request_data, $vars) {
        $current = (int) round(microtime(true) * 1000);
        $this->seed_clean($current);

        if($this->seed_check($request_data["user_id"], $request_data["seed"], $request_data["timestamp"], $current)) {
            return true;
        }

        return ["status" => false, "data" => ["error" => 403, "message" => "Replay detected"]];
    }

    private function seed_check($user_id, $seed, $timestamp, $current_time) {

        if($current_time < ($timestamp - $this->WINDOW) || $current_time > ($timestamp + $this->WINDOW)) {
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

    private function seed_clean($time) {
        $rand = rand(0, 100);
        if ($rand > $this->CLEAN_RATE) {
            return;
        }

        $shift = (60000 * $this->TIMEOUT) + $time;
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