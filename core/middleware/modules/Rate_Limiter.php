<?php
namespace middleware\modules;

class Rate_Limiter {
    private $DB;
    private $SQLITE;
    private $TIME_BUFFER;
    private $RATE_LIMIT;

    public function __construct($db, $sqlite) {
        $this->DB = $db;
        $this->SQLITE = $sqlite;
        $env_bootstrap = new \utils\Env_Bootstrap();
        $this->TIME_BUFFER = intval($env_bootstrap->get_var("TIME_BUFFER"));
        $this->RATE_LIMIT = intval($env_bootstrap->get_var("RATE_LIMIT"));
    }

    public function run($route_data, $request_data) {
        $out = $this->check_rate();

        if($out) {
            return true;
        }

        return ["status" => false, "data" => ["error" => 429, "message" => "Too many requests, please try again later"]];
    }

    private function check_rate () {
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $query = "SELECT * FROM rate_limits WHERE ip = :ip";
        $val_array = [
            [
                "name" => ":ip",
                "value" => $user_ip,
                "type" => "s"
            ]
        ];

        try {
            $resp = $this->SQLITE->make_query("select", $query, $val_array);
        }
        catch (\PDOException $e) {
            $log = new \utils\Log_Handler($this->SQLITE);
            $log->log("SQLITE Error", $e->getMessage() . "----" . $e->getTraceAsString(), null);
            return false;
        }
        $current_time = (int) round(microtime(true) * 1000);;
        if($resp) {
            if($resp[0]["last_request"]  + (int) $this->TIME_BUFFER >= $current_time) {
                if($resp[0]["request_count"] >= $this->RATE_LIMIT) {
                    return false;
                }
                else {
                    $query = "UPDATE rate_limits SET request_count = request_count + 1 WHERE ip = :ip";
                    $val_array = [
                        [
                            "name" => ":ip",
                            "value" => $user_ip,
                            "type" => "s"
                        ]
                    ];
                    $this->SQLITE->make_query("update", $query, $val_array);
                    return true;
                }
            }
            else {
                $query = "UPDATE rate_limits SET request_count = 1, last_request = :time WHERE ip = :ip";
                $val_array = [
                    [
                        "name" => ":time",
                        "value" => $current_time,
                        "type" => "i"
                    ],
                    [
                        "name" => ":ip",
                        "value" => $user_ip,
                        "type" => "s"
                    ]
                ];
                $this->SQLITE->make_query("update", $query, $val_array);
                return true;
            }
        }
        else {
            $query = "INSERT INTO rate_limits (ip, request_count, last_request) VALUES (:ip, 1, :time)";
            $val_array = [
                [
                    "name" => ":ip",
                    "value" => $user_ip,
                    "type" => "s"
                ],
                [
                    "name" => ":time",
                    "value" => $current_time,
                    "type" => "i"
                ]
            ];
            $this->SQLITE->make_query("insert", $query, $val_array);
            return true;
        }
    }
}