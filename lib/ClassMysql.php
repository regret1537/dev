<?php
    class ClassMysql
    {
        /**
         * Mysql Host
         * @var string
         */
        private $host = '0.0.0.0';

        /**
         * Mysql User
         * @var string
         */
        private $user = 'user';

        /**
         * Mysql Password
         * @var string
         */
        private $pass = 'password';

        /**
         * Mysql Resource
         * @var resource
         */
        private $resource = null;

        /**
         * Construct
         * @param string $host     Mysql Host
         * @param string $user     Mysql User
         * @param string $password Mysql Password
         */
        public function __construct($host, $user, $password)
        {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $password;
        }

        /**
         * Connect to Mysql
         */
        public function connect()
        {
            $this->resource = mysql_connect($this->host, $this->user, $this->pass);
            if ($this->resource === false) {
                throw new Exception('10');
            }
        }

        /**
         * Set Mysql use DB
         * @param string $name Mysql DB Name
         */
        public function set_db_name($name)
        {
            if (mysql_select_db($name, $this->resource) === false) {
                throw new Exception('11');
            }
        }

        /**
         * Set Mysql encode
         * @param string $enc Mysql encode
         */
        public function set_db_enc($enc)
        {
            if (mysql_query('SET NAMES ' . $enc, $this->resource) === false) {
                throw new Exception('12');
            }
        }

        /**
         * Query Mysql DB
         * @param  string $sql SQL
         * @return array       Query Data
         */
        public function query($sql)
        {
            $data = [];
            $result = mysql_query($sql, $this->resource);
            if ($result === false) {
                throw new Exception('13');
            }

            if (mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    array_push($data, $row);
                }
            }
            mysql_free_result($result);
            return $data;
        }

        /**
         * Close Mysql connection
         */
        public function close()
        {
            mysql_close($this->resource);
        }
    }