<?php
include_once __DIR__ . '/../Models/Registration.php';
class RegistrationRepository {
    private $conn;
    private $table_name = "registrations";
    public function __construct($db) {
        $this->conn = $db;
    }
    public function register($userId, $eventId) {

        try {
            $check = "SELECT id, status
                      FROM " . $this->table_name . "
                      WHERE user_id = ?
                      AND event_id = ?";
            $stmtCheck = $this->conn->prepare($check);
            $stmtCheck->execute([$userId, $eventId]);
            $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                // Nếu bị từ chối thì cho đăng ký lại
                if ($existing['status'] == 2) {
                    $update = "UPDATE " . $this->table_name . "
                               SET status = 0
                               WHERE id = ?";
                    $stmtUpdate = $this->conn->prepare($update);
                    return $stmtUpdate->execute([$existing['id']]);
                }
                return false;
            }
            $query = "INSERT INTO " . $this->table_name . "
                     (user_id, event_id, status)
                     VALUES (?, ?, 0)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$userId, $eventId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getAllWithDetails($userId) {

        // Lấy thông tin user
        $userQuery = "SELECT role, club_id
                      FROM users
                      WHERE id = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        // ADMIN => xem toàn bộ
        if ($user['role'] === 'admin') {
            $query = "SELECT
                        r.id,
                        u.full_name,
                        e.title as event_title,
                        r.status
                      FROM registrations r
                      JOIN users u ON r.user_id = u.id
                      JOIN events e ON r.event_id = e.id
                      ORDER BY r.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // CHỦ NHIỆM => chỉ xem CLB mình
        $query = "SELECT
                    r.id,
                    u.full_name,
                    e.title as event_title,
                    r.status
                  FROM registrations r
                  JOIN users u ON r.user_id = u.id
                  JOIN events e ON r.event_id = e.id
                  WHERE e.club_id = ?
                  ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user['club_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $userId) {
        $userQuery = "SELECT role, club_id
                      FROM users
                      WHERE id = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        // Nếu không phải admin => kiểm tra quyền
        if ($user['role'] !== 'admin') {
            $checkQuery = "SELECT e.club_id
                           FROM registrations r
                           JOIN events e ON r.event_id = e.id
                           WHERE r.id = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([$id]);
            $event = $checkStmt->fetch(PDO::FETCH_ASSOC);
            // Không đúng CLB
            if (!$event || $event['club_id'] != $user['club_id']) {
                return false;
            }
        }
        $query = "UPDATE " . $this->table_name . "
                  SET status = :status
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function getByUser($userId) {
        $query = "SELECT
                    e.title,
                    e.event_date,
                    e.location,
                    r.status
                  FROM events e
                  JOIN registrations r
                  ON e.id = r.event_id
                  WHERE r.user_id = ?
                  ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}