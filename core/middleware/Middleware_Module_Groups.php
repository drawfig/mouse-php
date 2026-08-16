<?php
namespace middleware;

class Middleware_Module_Groups {
    public $GLOBAL_MIDDLEWARE = [
        "Format_Validation",
        "Rate_Limiter",
        "Replay_Check"
    ];

    public $GROUP_MIDDLEWARE = [
        "secure_routes" => [
            "Authenticate"
        ],
    ];
}