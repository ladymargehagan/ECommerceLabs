<?php
include_once 'db_cred.php';

/**
 * Database connection and query class
 */
if (!class_exists('db_connection')) {
    class db_connection
    {
        // Properties
        public $db = null;
        public $results = null;

        /**
         * Establish a database connection
         * @return bool
         */
        function db_connect()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }
            return true;
        }

        /**
         * Get active DB connection (or false on failure)
         */
        function db_conn()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }
            return $this->db;
        }

        /**
         * Run a SELECT query
         * @param string $sqlQuery
         * @return bool
         */
        function db_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $this->results = mysqli_query($this->db, $sqlQuery);
            return $this->results !== false;
        }

        /**
         * Run an INSERT, UPDATE, DELETE query
         * @param string $sqlQuery
         * @return bool
         */
        function db_write_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $result = mysqli_query($this->db, $sqlQuery);
            return $result !== false;
        }

        /**
         * Fetch a single record
         * @param string $sql
         * @return array|false
         */
        function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_assoc($this->results);
        }

        /**
         * Fetch all records
         * @param string $sql
         * @return array|false
         */
        function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_all($this->results, MYSQLI_ASSOC);
        }

        /**
         * Get count of rows in last result
         * @return int|false
         */
        function db_count()
        {
            if ($this->results == null || $this->results == false) {
                return false;
            }
            return mysqli_num_rows($this->results);
        }

        /**
         * Get last inserted ID
         * @return int
         */
        function last_insert_id()
        {
            return mysqli_insert_id($this->db);
        }
    }
}
