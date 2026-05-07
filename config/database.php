<?php
class Database {
    private $host = "localhost";
    private $db_name = "clb_management"; // Tên database từ file .sql của bạn
    private $username = "root";           // Mặc định của XAMPP
    private $password = "";               // Mặc định của XAMPP là trống
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>