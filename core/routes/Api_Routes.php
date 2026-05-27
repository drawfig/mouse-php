<?php
namespace routes;

class Api_Routes {
    public $ROUTES = [
        "/test" => ["method" => "get", "class" => "Test_Controller", "type" => "GET", "protected" => false],
    ];
}