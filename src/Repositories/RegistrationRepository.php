<?php
// src/Repositories/RegistrationRepository.php
include_once __DIR__ . '/../Models/Registration.php';

class RegistrationRepository {
    private $conn;
    private $table_name = "registrations";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đăng ký tham gia sự kiện
    public function register($userId, $eventId) {
    try {
        // 1. Kiểm tra xem user đã có đơn đăng ký nào cho sự kiện này chưa
        $check = "SELECT id, status FROM " . $this->table_name . " WHERE user_id = ? AND event_id = ?";
        $stmtCheck = $this->conn->prepare($check);
        $stmtCheck->execute([$userId, $eventId]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Nếu đã bị từ chối (status = 2), cho phép đăng ký lại bằng cách cập nhật về 0
            if ($existing['status'] == 2) {
                $update = "UPDATE " . $this->table_name . " SET status = 0 WHERE id = ?";
                $stmtUpdate = $this->conn->prepare($update);
                return $stmtUpdate->execute([$existing['id']]);
            }
            // Nếu đang chờ hoặc đã duyệt thì không cho đăng ký nữa
            return false; 
        }

        // 2. Nếu chưa từng đăng ký, chèn mới như bình thường
        $query = "INSERT INTO " . $this->table_name . " (user_id, event_id, status) VALUES (?, ?, 0)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId, $eventId]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

    // Lấy danh sách cho ADMIN duyệt
    public function getAllWithDetails() {
        $query = "SELECT r.id, u.full_name, e.title as event_title, r.status 
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN events e ON r.event_id = e.id
                  ORDER BY r.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    // Lấy danh sách sự kiện của 1 User (CHỈ ĐỂ LẠI 1 HÀM NÀY)
    public function getByUser($userId) {
        $query = "SELECT e.title, e.event_date, e.location, r.status 
                  FROM events e 
                  JOIN registrations r ON e.id = r.event_id 
                  WHERE r.user_id = ? 
                  ORDER BY r.id DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}