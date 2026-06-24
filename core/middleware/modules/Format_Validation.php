<?php
namespace middleware\modules;

class Format_Validation {
    private $DB;
    private $LOG;

    private $VALID_FIELDS =[
        "user_id",
        "api_version",
        "data",
        "auth",
    ];

    public function __construct($db, $sqlite) {
        $this->DB = $db;
        $this->LOG = new \utils\Log_Handler($sqlite);
    }

    public function run($route_data, $request_data) {
        if($route_data["type"] != "GET") {
            return $this->field_check(sizeof($this->VALID_FIELDS), $request_data);
        }

        return true;
    }

    private function field_check($size, $request) {

        if(!is_array($request) || sizeof($request) !== $size) {
            $this->LOG->log("Error", "400 error: Invalid Formating, Request either doesn't exist or is not the right size", null);
            return ["status" => false, "data" => ["error" => 400, "message" =>"Invalid Formating"]];
        }

        $fused = true;
        foreach($request as $key => $value) {
            if(in_array($key, $this->VALID_FIELDS)) {
                $fuse = $this->type_check($key, $value);
            }
            else {
                $this->LOG->log("Error", "400 error: Invalid Formating, The field names are not correct", null);
                return ["status" => false, "data" => ["error" => 400, "message" =>"Invalid Formating"]];
            }

            if(!$fuse) {
                $this->LOG->log("Error", "400 error: Invalid Formating, The field data types are incorrect", null);
                return ["status" => false, "data" => ["error" => 400, "message" =>"Invalid Formating"]];
            }
        }

        return true;
    }

    private function type_check($field, $data) {
        switch($field) {
            case "user_id":
                if(is_int($data)) {
                    return true;
                }
                break;
            case "api_version":
            case "auth":
                if(is_string($data)) {
                    return true;
                }
                break;
            case "data":
                if(is_array($data)) {
                    return true;
                }
                break;
            default:
                return false;
        }

        return false;
    }
}