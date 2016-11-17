<?php
    class ClassCsv
    {
        /**
         * CSV Path
         * @var string
         */
        private $path = 'path';

        /**
         * Construct
         * @param string $path CSV Path
         */
        public function __construct($path)
        {
            $this->path = $path;
        }

        /**
         * Parse CSV data to array
         * @return array CSV data
         */
        public function get_data()
        {
            $data = [];
            $rows = file($this->path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
            foreach ($rows as $index => $row) {
                if ($index > 0) {
                    $row_data = explode(',', $row);
                    array_push($data, $row_data);
                }
            }
            return $data;
        }

    }