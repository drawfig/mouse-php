<?php

namespace utils;

use PDOException;

 class Sqlite_Handler {
     public $DB;
     public function __construct() {
         $file = __DIR__ . "/../../mouse.db";
         try {
             $this->DB = new \PDO("sqlite:$file");
             $this->DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
         }
         catch(PDOException $e) {
             echo $e->getMessage();
             die();
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

         try {
             foreach ($val_array as $val) {
                 $ready_query->bindValue($val["name"], $val["value"], $this->pdo_type_sort($val["type"]));
             }
         }
         catch (\PDOException $e) {
             echo $e->getMessage();
             die();
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