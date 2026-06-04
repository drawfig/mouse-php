<?php
namespace Page_Engine;

class Page_Engine {
    private $VIEW_SCRIPTS;
    private $VIEW_STYLES;
    private $VIEW_TITLES;
    private $ERROR_TITLES;
    public $VIEW_NAME;
    public $FAVICON_PATH;
    public $VIEW_DATA;
    public $VIEW_PAGE;
    public $DEV_MODE;


    public function __construct() {
        $view_configs = new \Page_Engine\View_Configs();
        $this->VIEW_SCRIPTS = $view_configs->VIEW_SCRIPTS;
        $this->VIEW_STYLES = $view_configs->VIEW_STYLES;
        $this->VIEW_TITLES = $view_configs->VIEW_TITLES;
        $this->FAVICON_PATH = $view_configs->FAVICON_PATH;
        $this->ERROR_TITLES = $view_configs->ERROR_TITLES;
        $env = new \Utils\Env_Bootstrap();
        $this->DEV_MODE = $env->get_var("DEV_MODE");
    }

    public function open_view($view_name, $data, $error = false) {

        if(file_exists(__DIR__ . "/../display_pages/views/{$view_name}.php") && !$error) {
            $this->load_template($view_name);
        }
        else {
            $this->load_error_template($view_name);
        }
    }

    private function format_scripts() {
        $out = "";
        foreach ($this->VIEW_SCRIPTS as $script) {
            $out .= "<script src='resources/scripts/{$script}'></script>";
        }

        if($this->DEV_MODE) {
            $out .= "<script src='resources/scripts/dev_mode/dev_mode.js'></script>";
        }

        return $out;
    }

    private function format_styles() {
        $out = "";
        foreach ($this->VIEW_STYLES as $style) {
            $out .= "<link rel='stylesheet' href='resources/styles/{$style}' />";
        }

        return $out;
    }

    private function title_format($view_name) {
        if(array_key_exists($view_name, $this->VIEW_TITLES)) {
            return $this->VIEW_TITLES[$view_name];
        }
        else {
            return $this->VIEW_TITLES["default"];
        }
    }

    private function error_title_format($view_name) {
        if(array_key_exists($view_name, $this->ERROR_TITLES)) {
            return $this->ERROR_TITLES[$view_name];
        }
        return $this->ERROR_TITLES["default"];
    }

    private function load_template($view_name) {
        $this->VIEW_NAME = $view_name;

        include("template.php");
    }

    private function load_error_template($view_name) {
        $this->VIEW_NAME = $view_name;

        include("error_template.php");
    }

    private function get_favicon() {
        return "<link rel='icon' href={$this->FAVICON_PATH} />";
    }

    private function view_render($view_name) {
        include(__DIR__ . "/../display_pages/views/{$view_name}.php");
    }

    private function error_render($view_name) {
        include(__DIR__ . "/../display_pages/error_pages/{$view_name}.php");
    }
}