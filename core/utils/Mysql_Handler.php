<?php
namespace utils;
use PDO;
use PDOException;

class Mysql_Handler{
    private $DB_HOST;
    private $DB_PORT;
    private $DB_NAME;
    private $DB_USER;
    private $DB_PASS;
    private $DB;

    public function __construct() {
        $this->init();
    }

    private function init() {
        $property_provider = new \Utils\Env_Bootstrap("db");
        $this->DB_HOST = $property_provider->get_var("DB_HOST");
        $this->DB_PORT = $property_provider->get_var("DB_PORT");
        $this->DB_NAME = $property_provider->get_var("DB_NAME");
        $this->DB_USER = $property_provider->get_var("DB_USERNAME");
        $this->DB_PASS = $property_provider->get_var("DB_PASSWORD");

        try {
            $this->DB = new \PDO("mysql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_NAME}", $this->DB_USER, $this->DB_PASS);
        }
        catch(\PDOException $e) {
            $this->DB = null;
            error_log("Mysql connection failed: " . $e->getMessage() . "----");
        }
    }

    public function make_query($type, $query, $var_array) {
        try {
            switch ($type) {
                case "insert":
                    $output = $this->insert_query($query, $var_array);
                    break;
                case "delete":
                    $output = $this->delete_query($query, $var_array);
                    break;
                case "update":
                    $output = $this->update_query($query, $var_array);
                    break;
                case "select":
                default:
                    $output = $this->basic_query($query, $var_array);
                }
                return $output;
        }
        catch (\PDOException $e) {
            error_log("Mysql query failed: " . $e->getMessage() . "----");
            $this->DB = null;
            return false;
        }
    }

    private function insert_query($query, $val_array) {
        $ready_query = $this->DB->prepare($query);
        foreach($val_array as $val) {
            $ready_query->bindValue($val["name"], $val["value"], $this->pdo_type_sort($val["type"]));
        }

        $ready_query->execute();
        return $this->DB->lastInsertId();
    }

    private function basic_query($query, $val_array) {
        $ready_query = $this->DB->prepare($query);
        if($val_array) {
            foreach ($val_array as $val) {
                $ready_query->bindValue($val["name"], $val["value"], $this->pdo_type_sort($val["type"]));
            }
        }
        $ready_query->execute();
        $output = $ready_query->fetchAll(\PDO::FETCH_ASSOC);
        return $output;
    }

    private function delete_query($query, $val_array) {
        $ready_query = $this->DB->prepare($query);
        if($val_array) {
            foreach ($val_array as $val) {
                $ready_query->bindValue($val["name"], $val["value"], $this->pdo_type_sort($val["type"]));
            }
        }
        $ready_query->execute();
        return true;
    }

    private function update_query($query, $val_array)
    {
        $ready_query = $this->DB->prepare($query);
        if($val_array) {
            foreach ($val_array as $val) {
                $ready_query->bindValue($val["name"], $val["value"], $this->pdo_type_sort($val["type"]));
            }
        }
        $ready_query->execute();
        return true;
    }

    private function pdo_type_sort($type) {
        switch ($type) {
            case "i":
                return \PDO::PARAM_INT;
            case "b":
                return \PDO::PARAM_BOOL;
            case "s":
            default:
                return \PDO::PARAM_STR;
        }
    }

    public function __destruct() {
        $this->DB = null;
    }
}
