<?php
spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/controllers/' . str_replace("controllers\\", "", $className) . '.php')) {
        require_once (__DIR__ . '/controllers/' . str_replace("controllers\\", "", $className) . '.php');
    }
});


include_once("./core/controllers/{$argv[1]}.php");
var_dump(file_exists("./core/controllers/{$argv[1]}.php"));
$con_name = "controllers\\{$argv[1]}";
$handler = new $con_name(1, 2);
$output = get_class_methods($handler);
echo json_encode($output);