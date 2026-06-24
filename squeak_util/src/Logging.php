<?php

class Logging extends mouse_hole {
    private $FILE_TIME = 0;
    private $RUN = true;

    public function show() {
        pcntl_signal(SIGINT, function () {
            print(ANSI_CLEAR_LINE . "\n");
            print($this->LINE_BREAK);
            print("Exiting Log Display.\n");
            print($this->LINE_BREAK);
            $this->RUN = false;
            print("\e[?25h");
        });

        while($this->RUN) {
            clearstatcache();
            $file_chk = filemtime("./mouse.db");
            if($file_chk != $this->FILE_TIME) {
                $this->FILE_TIME = $file_chk;
                $this->log_display();
                $this->check_db();
            }
            pcntl_signal_dispatch();
            sleep(1);
        }

        system('stty sane');
        system('tput cnorm');
        $this->clear_screen();
    }

    private function log_display() {
        system("clear");
        print($this->LINE_BREAK);
        print("                           Log Display\n");
        print("                       To quit press Ctrl+c \n");
        print($this->LINE_BREAK);

    }

    private function get_db() {
        include_once("sqlite_bootstrap.php");
        $db = new sqlite_bootstrap();
        return $db;
    }

    private function check_db() {
        $db = $this->get_db();
        if(!$db) {
            return;
        }
        $query = "SELECT * FROM server_log ORDER BY id DESC";
        $ready = $db->prepare($query);
        $run = $ready->execute();
        $logs = [];
        while ($result = $run->fetchArray(SQLITE3_ASSOC)) {
            $logs[] = $result;
        }
        $db = null;
        $logs = $this->process_timestamps($logs);
        $title_row = ["ID", "User ID", "Message Type", "Description", "Timestamp"];
        print("\e[?25l");
        $this->make_table($title_row, $logs);
        print("\e[?25h");
    }

    private function process_timestamps($logs) {
        $output = [];
        foreach($logs as $log) {
            $log["timestamp"] = date("Y-m-d H:i:s", ($log["timestamp"] / 1000));
            $output[] = $log;
        }
        return $output;
    }
}