<?php

class log_handler extends cheese_core {
    private $run = true;
    private $file_time;

    public function run_logging() {
        if($this->server_files_check()) {
            if($this->sqlite3_check()) {
                $this->display_logs();
            }
            else {
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[31mMissing dependency:");
                print("\033[31mSQLite3 is not installed.\n");
                print("\033[31mPlease check how to install SQLite3 on your distro and rerun the wand 'run-logging' command.\n");
                print("\033[31m$this->LINE_BREAK\n");
                print("\033[0m");
            }
        }
        else {
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[31mServer files missing:");
            print("\033[31mPlease run the wand 'init' command first to install the server.\n");
            print("\033[31m$this->LINE_BREAK\n");
            print("\033[0m");
        }
    }

    private function display_logs() {
        pcntl_signal(SIGINT, function () {
            print(ANSI_CLEAR_LINE . "\n");
            print($this->LINE_BREAK);
            print("Exiting Log Display.\n");
            print($this->LINE_BREAK);
            $this->run = false;
            print("\e[?25h");
        });
        while ($this->run) {
            clearstatcache();
            $file_chk = filemtime("Emberwhisk/src/web_sock.db");
            if($file_chk != $this->file_time) {
                $this->file_time = $file_chk;
                $logs = $this->get_logs();
                $logs_processed = $this->process_timestamps($logs);
                $title_row = ["ID", "User ID", "Message Type", "Description", "Timestamp"];
                print("\e[?25l");
                $this->log_display();
                $this->make_table($title_row, $logs_processed);
            }
            pcntl_signal_dispatch();
            sleep(1);
        }
    }

    private function log_display() {
        system("clear");
        print($this->LINE_BREAK);
        print("                           Log Display\n");
        print("                       To quit press Ctrl+c \n");
        print($this->LINE_BREAK);

    }

    private function process_timestamps($logs) {
        $output = [];
        foreach($logs as $log) {
            $log["time_entered"] = date("Y-m-d H:i:s", ($log["time_entered"] / 1000));
            $output[] = $log;
        }
        return $output;
    }

    private function get_logs() {
        include_once("wand_sqlite.php");
        $db = new Sqlite_DB();
        $ready_query = $db->prepare("SELECT * FROM server_log ORDER BY id DESC");
        $run_query = $ready_query->execute();
        $output = [];
        while ($result = $run_query->fetchArray(SQLITE3_ASSOC)) {
            $output[] = $result;
        }
        $db->close();
        $db = null;
        return $output;
    }
}