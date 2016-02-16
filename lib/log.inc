<?php
    class log {
        # 參數宣告
        private $log_dir_path = 'log_dir_path';
        private $log_file_name = 'log_file_name';
        private $log_file_path = 'log_file_path';
        
        # 建構子
        function __construct($log_title = '', $log_dir = './log') {
            # 設定 log 目錄路徑
            $this->log_dir_path = $log_dir;
            
            # 設定 log 名稱
            if (empty($log_title)) {
                $this->log_file_name = 'tmp_log_' . date('Ymd') . '.txt';
            } else {
                $this->log_file_name = $log_title . '_' . date('Ymd') . '.txt';
            }
            
            # 設定 log 路徑
            $this->log_file_path = $this->log_dir_path . '/' . $this->log_file_name;
        }
        
        # 記錄 log
        public function write_log($cont, $is_append = true) {
            $log_cont = date('Y-m-d H:i:s') . "\t" . $cont . "\n";
            if ($is_append) {
                file_put_contents($this->log_file_path, $log_cont, FILE_APPEND);
            } else {
                file_put_contents($this->log_file_path, $log_cont);
            }
            
        }
    }
?>