<?php
namespace controllers;

class Home_Controller {
    public $REQUEST_DATA;
    public $RESP;

    public function __construct($db, $sqlite, $request_data) {
        $this->REQUEST_DATA = $request_data;
        if($db !== 1) {
            $this->RESP = new \utils\Response_Handler();
        }
    }

    public function home_page() {
        $page_engine = new \Page_Engine\Page_Engine();

        $page_engine->open_view("Home", []);
    }
}