<?php

class sqlite_bootstrap extends SQLite3 {
    public function __construct() {
        $this->open('./mouse.db');
    }
}