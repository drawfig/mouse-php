<?php
namespace utils;
include_once realpath(__DIR__ . "/../../vendor/autoload.php");

class Env_Bootstrap {
    private $ENV;
    private $APP_NAME;
    private $APP_VERSION;
    private $APP_VERSION_NAME;
    private $ADDRESS;
    private $PROTOCOL;
    private $ENVIRONMENT;
    private $SECRET;
    private $WEBSOCKET_ADDRESS;
    private $WEBSOCKET_KEY;
    private $WEBSOCKET_PROTOCOL;
    private $WEBSOCKET_PORT;
    private $FRONT_END_ADDRESS;
    private $PEPPER;
    private $TIME_BUFFER;
    private $RATE_LIMIT;
    private $MYSQL_RUN;
    private $DB_HOST;
    private $DB_PORT;
    private $DB_NAME;
    private $DB_USERNAME;
    private $DB_PASSWORD;
    private $RUN_TYPE;

    public function __construct($type = "app") {
        $this->set_environment();
        switch ($type) {
            case "db":
                $this->db_init();
                break;
            default:
                $this->init();
        }
    }

    private function set_environment() {
        $this->ENV = "local";
        $env_arg = getenv("PHP_ENV");
        if(!!$env_arg) {
            switch ($env_arg) {
                case "local":
                case "dev":
                case "test":
                case "prod":
                    $this->ENV = $env_arg;
                    break;
            }
        }
        $this->RUN_TYPE = $this->ENV;
    }

    private function init() {
        $dotenv = \Dotenv\Dotenv::createImmutable(realpath(__DIR__ . "/../"), ".env.{$this->ENV}");
        $dotenv->load();
        $this->APP_NAME = $_ENV['APP_NAME'];
        $this->APP_VERSION = $_ENV['APP_VERSION'];
        $this->APP_VERSION_NAME = $_ENV['APP_VERSION_NAME'];
        $this->ADDRESS = $_ENV['ADDRESS'];
        $this->PROTOCOL = $_ENV['PROTOCOL'];
        $this->ENVIRONMENT = $_ENV['ENVIRONMENT'];
        $this->SECRET = $_ENV['SECRET'];
        $this->WEBSOCKET_ADDRESS = $_ENV['WEBSOCKET_ADDRESS'];
        $this->WEBSOCKET_KEY = $_ENV['WEBSOCKET_KEY'];
        $this->WEBSOCKET_PROTOCOL = $_ENV['WEBSOCKET_PROTOCOL'];
        $this->WEBSOCKET_PORT = $_ENV['WEBSOCKET_PORT'];
        $this->FRONT_END_ADDRESS = $_ENV['FRONT_END_ADDRESS'];
        $this->PEPPER = $_ENV['PEPPER'];
        $this->TIME_BUFFER = $_ENV['TIME_BUFFER'];
        $this->RATE_LIMIT = $_ENV['RATE_LIMIT'];
        $this->MYSQL_RUN = $_ENV['MYSQL_RUN'];
    }

    private function db_init() {
        $dotenv = \Dotenv\Dotenv::createImmutable(realpath(__DIR__ . "/../"), ".env.db_config");
        $dotenv->load();
        $this->DB_HOST = $_ENV['DB_HOST'];
        $this->DB_PORT = $_ENV['DB_PORT'];
        $this->DB_NAME = $_ENV['DB_NAME'];
        $this->DB_USERNAME = $_ENV['DB_USERNAME'];
        $this->DB_PASSWORD = $_ENV['DB_PASSWORD'];
    }

    public function get_var($var_key) {
        if(isset($this->$var_key)) {
            return $this->$var_key;
        }
        return -99999999;
    }
}