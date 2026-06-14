<?php
namespace middleware;

class Middleware_Module_Groups {
    public $GLOBAL_MIDDLEWARE = [
        "Authenticate",
        "Format_Validation"
    ];

    public $GROUP_MIDDLEWARE = [
        "example_group" => [
            "Example_Middleware"
        ],
    ];
}