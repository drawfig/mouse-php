<?php
namespace models;

class User_Model {
    public function create_user($user_data, $db) {
        $hash_gen = new \utils\Hash_Gen();
        $salt = $hash_gen->salt();
        $key = $hash_gen->salt();
        $join_date = floor(microtime(true) * 1000);
        $hash = $hash_gen->hash($user_data["password"], $salt);

        $query = "INSERT INTO users (username, join_date, salt, hash, key) VALUES (:username, :join_date, :salt, :hash, :key)";

        $params = [
            [
                "name" => ":username",
                "value" => $user_data["username"],
                "type" => "s"
            ],
            [
                "name" => ":join_date",
                "value" => $join_date,
                "type" => "i"
            ],
            [
                "name" => ":salt",
                "value" => $salt,
                "type" => "s"
            ],
            [
                "name" => ":hash",
                "value" => $hash,
                "type" => "s"
            ],
            [
                "name" => ":key",
                "value" => $key,
                "type" => "s"
            ]
        ];

        try {
            $db->make_query("insert", $query, $params);
        }
        catch(\Exception $e) {
            return false;
        }

        return true;
    }

    public function get_user_by_name($username, $db) {
        $query = "SELECT * FROM users WHERE username = :username";
        $params = [
            [
                "name" => ":username",
                "value" => $username,
                "type" => "s"
            ]
        ];

        try {
            return $db->make_query("select", $query, $params);
        }
        catch(\Exception $e) {
            return false;
        }
    }
}