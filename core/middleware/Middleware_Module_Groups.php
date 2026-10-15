<?php
namespace middleware;

class Middleware_Module_Groups {
    public $GLOBAL_MIDDLEWARE = [
        "Format_Validation",
        "Replay_Check"
    ];

    public $GROUP_MIDDLEWARE = [
        "secure_routes" => [
            "Authenticate"
        ],
    ];
}