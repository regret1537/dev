<?php
    class ClassZip
    {
        /**
         * Zip command output
         * @var array
         */
        private $output = [];

        /**
         * Zip command output code
         * @var integer
         */
        private $code = 0;

        /**
         * Zip files
         * @param  string $dir      Source dir
         * @param  string $zip_name Zip file name
         * @param  string $password Zip password
         */
       public function zipDir($dir = '', $zip_name = '', $password = '')
       {
          $command = 'zip';
          if ($password != '') {
              $command .= ' -P ' . $password;
          }
          $command .= ' -D -j ' . $zip_name . ' ' . $dir . '/*';
          exec($command, $this->output, $this->code);
       }

       /**
        * Get command result output
        * @return array output
        */
       public function getOutput()
       {
          return $this->output;
       }

       /**
        * Get command result code
        * @return integer code
        */
       public function getCode()
       {
          return $this->code;
       }
    }