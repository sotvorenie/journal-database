<?php
    class DB
    {
        private $host = "localhost";
        private $db = "host1878857";
        private $username = "host1878857";
        private $password = "cZFFbiGCK6";

        public $conn;

        public function getConnection(){
            // Создание подключения
            $this->conn = null;
            try{
                $this->conn = new mysqli($this->host, $this->db, $this->password, $this->username);
            }catch(PDOException $exception){
                echo "Database not connected: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }