<?php
namespace middleware;

class Middleware_Module_Groups {
    public $GLOBAL_MIDDLEWARE = [
        "Rate_Limiter",
        "User_Filter",
        "Format_Check",
        "Authenticate_User",
    ];

    public $GROUP_MIDDLEWARE = [
        "example_group" => [ "Example_Middleware" ],
    ];
}