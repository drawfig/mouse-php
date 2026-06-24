<?php
namespace utils;

class Log_Handler {
    private $DB;
    public function __construct($db) {
        $this->DB = $db;
    }

    public function log($type, $message, $user_id) {
        $timestamp = round(microtime(true) * 1000);
        $query = "INSERT INTO server_log (user_id, description, timestamp, message_type) VALUES (:user_id, :message, :timestamp, :type)";
        $vals_array = [
            [
                "name" => ":user_id",
                "value" => $user_id,
                "type" => "i"
            ],
            [
                "name" => ":message",
                "value" => $message,
                "type" => "s"
            ],
            [
                "name" => ":timestamp",
                "value" => $timestamp,
                "type" => "i"
            ],
            [
                "name" => ":type",
                "value" => $type,
                "type" => "s"
            ]
        ];
        try {
            $this->DB->make_query("insert", $query, $vals_array);
        }
        catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
}