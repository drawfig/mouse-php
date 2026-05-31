<?php
namespace Page_Engine;

class Page_Engine {
    private $VIEW_SCRIPTS;
    private $VIEW_STYLES;
    private $VIEW_TITLES;
    public $VIEW_NAME;
    public $FAVICON_PATH;
    public $VIEW_DATA;
    public $VIEW_PAGE;


    public function __construct() {
        $view_configs = new \Page_Engine\View_Configs();
        $this->VIEW_SCRIPTS = $view_configs->VIEW_SCRIPTS;
        $this->VIEW_STYLES = $view_configs->VIEW_STYLES;
        $this->VIEW_TITLES = $view_configs->VIEW_TITLES;
        $this->FAVICON_PATH = $view_configs->FAVICON_PATH;
    }

    public function open_view($view_name, $data) {
        if(file_exists(__DIR__ . "/../display_pages/views/{$view_name}.php")) {
            $this->load_template($view_name);
        }
        else {
            $this->get_error_page();
        }
    }

    private function format_scripts() {
        $out = "";
        foreach ($this->VIEW_SCRIPTS as $script) {
            $out .= "<script src='resources/scripts/{$script}'></script>";
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

    private function load_template($view_name) {
        $this->VIEW_NAME = $view_name;

        include("template.php");
    }

    private function get_error_page() {
        include(__DIR__ . "/../display_pages/error_pages/error_page.php");
    }

    private function get_favicon() {
        return "<link rel='icon' href={$this->FAVICON_PATH} />";
    }

    private function view_render($view_name) {
        include(__DIR__ . "/../display_pages/views/{$view_name}.php");
    }
}