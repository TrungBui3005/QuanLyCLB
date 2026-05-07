<?php
class ProfileRepository {
    private $conn;
    private $table_name = "users"; // Tên bảng từ file sql của bạn

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy dữ liệu thành viên từ database 'users'
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật thông tin vào bảng thanhvien
     */
public function updateUserInfo($data) { // Đổi tên hàm cho khớp với Service
    $query = "UPDATE " . $this->table_name . "  
              SET full_name = :full_name, email = :email 
              WHERE id = :id"; // Đổi 'hoten' thành 'full_name'
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email'     => $data['contact'],
            ':id'    => $data['id']
        ]);
    }
}
?>