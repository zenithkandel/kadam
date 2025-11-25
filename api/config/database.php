<?php
// api/config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "kadam_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // In production, log this error instead of showing it
            error_log("Connection error: " . $exception->getMessage());
            echo json_encode(["success" => false, "message" => "Database connection failed"]);
            exit;
        }

        return $this->conn;
    }
}
