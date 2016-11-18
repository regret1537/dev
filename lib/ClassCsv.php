<?php
    class ClassCsv
    {
        /**
         * Parse CSV data to array
         * @return array CSV data
         */
        public function getData($path)
        {
            $data = [];
            $rows = file($path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
            foreach ($rows as $index => $row) {
                if ($index > 0) {
                    $row_data = explode(',', $row);
                    array_push($data, $row_data);
                }
            }
            return $data;
        }

        /**
         * Set the data to CSV
         * @param string  $path   CSV path
         * @param array   $data   CSV data
         * @param boolean $append Is append
         * @return  boolean Set result
         */
        public function setData($path, $data = [], $append = false)
        {
            $row = $this->genRow($data);

            $result = false;
            if ($append === true) {
                $result = file_put_contents($path, $row, LOCK_EX|FILE_APPEND);
            } else {
                $result = file_put_contents($path, $row, LOCK_EX);
            }

            if ($result === false) {
                throw new Exception('21');
            }
            return $result;
        }

        /**
         * Generate a CSV row
         * @param  array  $data Row data
         * @return string       Row
         */
        public function genRow($data = [])
        {
            return implode(',', $data) . PHP_EOL;
        }

    }