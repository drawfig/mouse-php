<?php

include_once("src/Handlers/{$argv[1]}.php");
$handler = new $argv[1](1, 2, 3, 4, 5, 6);
$output = get_class_methods($handler);
echo json_encode($output);
