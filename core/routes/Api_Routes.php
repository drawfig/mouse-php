<?php
namespace routes;

class Api_Routes {
    public $ROUTES = [
        '/test' => ['class' => 'Test_Controller', 'method' => 'get', 'type' => 'GET', 'protected' => false],
        
    ];
}