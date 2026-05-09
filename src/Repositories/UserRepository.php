<?php
include_once __DIR__ . '/../Models/User.php';
class UserRepository {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getAll() {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row); 
        }
        return $users;
    }
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? AND password = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username, $password]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row);
        }
        return null;
    }
  public function register($data) {
    // Kiểm tra xem student_code đã có tài khoản chưa để tránh đăng ký trùng
    $check = "SELECT id FROM users WHERE student_code = ? OR username = ?";
    $stmtCheck = $this->conn->prepare($check);
    $stmtCheck->execute([$data->student_code, $data->username]);
    if($stmtCheck->rowCount() > 0) return false;

    $query = "INSERT INTO users (full_name, student_code, username, password, role) 
              VALUES (:full_name, :student_code, :username, :password, :role)";
    
    $stmt = $this->conn->prepare($query);
    
    return $stmt->execute([
        ':full_name' => $data->full_name,
        ':student_code' => $data->student_code,
        ':username' => $data->username,
        ':password' => $data->password,
        ':role' => $data->role
    ]);
}
    public function updateRole($id, $role, $club_id) {
    try {
        // Câu lệnh SQL cập nhật đồng thời cả Role và Club_id
        $query = "UPDATE users SET role = :role, club_id = :club_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':club_id', $club_id);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}
}
?>