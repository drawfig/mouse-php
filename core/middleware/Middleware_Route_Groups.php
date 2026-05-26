<?php
namespace middleware;

class Middleware_Route_Groups {
    public $GROUPS = [
        "example_group" => [
            "example_route",
            "example_route_2",
        ],
    ];

    public $GLOBAL_BYPASS_ROUTES = [
        "example_route",
    ];
}