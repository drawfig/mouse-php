<?php

class help_handler extends mouse_hole {
    private $BUILD = [
        ["help", "Shows this help menu, listing all available commands."],
        ["version", "Shows the current version of mouse-php and squeak."],
        ["init", "Initializes a new mouse-php project."],
        ["tail", "Starts a live log display of your project's logs."],
        ["deploy", "Creates a lightweight deployment package for your project, and deploys it to your server if configured."],
        ["rollback", "Rolls back the last deployment to your server to a selected snapshot."],
        ["serve", "Starts the built-in web server with hot reloading for your project."],
        ["gen-env", "Generates a new environment file for your project."],
        ["gen-db-config", "Generates a new database configuration file for your project."],
        ["gen-deployment-config", "Generates a new deployment configuration file for your project to tell squeak what to do when the 'deploy' command is run."],
        ["add-auth", "Adds a new authentication scaffold to your project."],
        ["add-controller", "Adds a new controller to your project."],
        ["add-model", "Adds a new model to your project."],
        ["add-view", "Adds a new view to your project."],
    ];
    private $MIDDLEWARE = [
        ["add-middleware", "Adds a new middleware module to your project."],
        ["show-middleware", "Shows all middleware modules in your project."],
        ["mk-group", "Creates a new middleware group."],
        ["rmv-group", "Removes a middleware group."],
        ["add-group-middleware", "Adds a middleware module to a middleware group."],
        ["rmv-group-middleware", "Removes a middleware module from a middleware group."],
        ["add-group-route", "Adds a route to a middleware group."],
        ["rmv-group-route", "Removes a route from a middleware group."],
        ["add-global-middleware", "Adds a middleware module to the global middleware stack."],
        ["rmv-global-middleware", "Removes a middleware module from the global middleware stack."],
        ["add-global-bypass", "Adds a route to the global bypass stack."],
        ["rmv-global-bypass", "Removes a route from the global bypass stack."],
    ];
    private $ROUTES = [
        ["add-route", "Adds a new route to your project."],
        ["show-routes", "Shows all routes in your project."],
        ["rmv-route", "Removes a route from your project."],
    ];

    private $VERSION = [
        "mouse-php Version" => "RC 1.0.0",
        "squeak Version" => "RC 1.0.0",
        "Codename" => "Arrowhead",
        "Build Date" => "06/24/2026",
        "Developed By" => "Kurtis Milliren",
        "Contact me on Discord" => "drawfig",
        "Or Find me on Github" => "https://github.com/drawfig",
    ];

    public function help() {
        $options = [
            "Build/Management Commands",
            "Middleware Commands",
            "Routes Commands",
        ];

        $type = $this->menu($options, "What Category of command would you like to see?", true);

        switch($type) {
            case "Build/Management Commands":
                $output = $this->BUILD;
                break;
            case "Middleware Commands":
                $output = $this->MIDDLEWARE;
                break;
            case "Routes Commands":
                $output = $this->ROUTES;
                break;
            default:
                return;
        }

        print($this->LINE_BREAK);
        print($type . "\n");
        print($this->LINE_BREAK);

        $this->make_table(["Command", "Description"], $output);
    }

    public function show_version() {
        system("clear");
        print_r($this->logo);
        print($this->LINE_BREAK);
        print("Version Information:\n");
        print($this->LINE_LOWER);
        foreach ($this->VERSION as $key => $value) {
            print("{$key}: {$value}\n");
        }
        print($this->LINE_BREAK);
    }
}