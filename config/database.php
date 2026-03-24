<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
            $this->host = "localhost";
            $this->db_name = "pushforgood";
            $this->username = "root";
            $this->password = "";
        } else {
            $this->host = "sql310.infinityfree.com";
            $this->db_name = "if0_41294906_pushforgood";
            $this->username = "if0_41294906";
            $this->password = "j3xF3LtLQnqSC";
        }

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}