<?php
include_once 'db_cred.php';

// Database connection and query class
if (!class_exists('db_connection')) {
    class db_connection
    {

        public $db = null;
        public $results = null;

    // Establish a database connection
        function db_connect()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }
            
            // Set character encoding to UTF-8
            mysqli_set_charset($this->db, "utf8mb4");
            
            return true;
        }

    // Get active DB connection (or false on failure)
        function db_conn()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

            if (mysqli_connect_errno()) {
                return false;
            }
            
            // Set character encoding to UTF-8
            mysqli_set_charset($this->db, "utf8mb4");
            
            return $this->db;
        }

    // Run a SELECT query
        function db_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $this->results = mysqli_query($this->db, $sqlQuery);
            if ($this->results === false) {
                error_log("Database query failed: " . mysqli_error($this->db) . " SQL: " . $sqlQuery);
            }
            return $this->results !== false;
        }

    // Run an INSERT, UPDATE, DELETE query
        function db_write_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $result = mysqli_query($this->db, $sqlQuery);
            return $result !== false;
        }

    // Fetch a single record
        function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_assoc($this->results);
        }

    // Fetch all records
        function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            $result = mysqli_fetch_all($this->results, MYSQLI_ASSOC);
            return $result !== false ? $result : array();
        }

    // Get count of rows in last result
        function db_count()
        {
            if ($this->results == null || $this->results == false) {
                return false;
            }
            return mysqli_num_rows($this->results);
        }

    // Get last inserted ID
        function last_insert_id()
        {
            return mysqli_insert_id($this->db);
        }
    }
}
