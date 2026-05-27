<?php
namespace middleware;

class Middleware_Module_Groups {
    public $GLOBAL_MIDDLEWARE = [
        "Format_Validation",
        "Authenticate",
    ];

    public $GROUP_MIDDLEWARE = [
        "example_group" => [ "Example_Middleware" ],
    ];
}