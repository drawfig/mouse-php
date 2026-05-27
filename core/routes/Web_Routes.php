<?php
namespace routes;

class Web_Routes {
    public $ROUTES = [
        "/" => ["method" => "home_page", "class" => "Home_Controller", "type" => "GET", "protected" => false],
    ];
}