<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database
{
    private $host = "localhost";
    private $dbName = "pushforgood";
    private $username = "root"; // Default XAMPP/MAMP user
    private $password = "";     // Default XAMPP is empty, MAMP is "root"
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
