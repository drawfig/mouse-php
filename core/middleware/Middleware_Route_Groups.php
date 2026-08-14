<?php
namespace middleware;

class Middleware_Route_Groups {
    public $GROUPS = [
        "secure_routes" => [
            "/example_route",
        ],
    ];

    public $GLOBAL_BYPASS_ROUTES = [
        "/example_route"
    ];
}