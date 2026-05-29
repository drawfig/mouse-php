<?php

class Sqlite_DB extends SQLite3 {
    public function __construct() {
        $this->open('Emberwhisk/src/web_sock.db');
    }
}