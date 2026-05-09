<?php
class RegistrationClubRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($userId, $clubId, $reason) {
        try {
            // Kiểm tra xem đã đăng ký CLB này chưa để tránh trùng lặp
            $checkSql = "SELECT id FROM club_registrations WHERE user_id = ? AND club_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$userId, $clubId]);
            
            if ($checkStmt->rowCount() > 0) return false;

            $sql = "INSERT INTO club_registrations (user_id, club_id, reason, status) VALUES (?, ?, ?, 'Chờ duyệt')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $clubId, $reason]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAllWithDetails($managedClubId = null) {
        $sql = "SELECT r.*, u.full_name, u.student_code, c.club_name 
                FROM club_registrations r
                JOIN users u ON r.user_id = u.id
                JOIN clubs c ON r.club_id = c.id";
        
        // Nếu là chủ nhiệm, lọc theo club_id được gán trong bảng users
        if ($managedClubId !== null && $managedClubId !== '') {
            $sql .= " WHERE r.club_id = ?";
            $sql .= " ORDER BY r.registered_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$managedClubId]);
        } else {
            // Admin lấy tất cả
            $sql .= " ORDER BY r.registered_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        try {
            $this->db->beginTransaction();

            // 1. Cập nhật trạng thái trong bảng club_registrations
            $sql = "UPDATE club_registrations SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$status, $id]);

            // 2. Nếu duyệt thành công, tự động thêm user vào bảng 'member'
            if ($result && $status === 'Đã duyệt') {
                // Lấy thông tin user_id và club_id từ đơn đăng ký vừa duyệt
                $getInfo = $this->db->prepare("SELECT user_id, club_id FROM club_registrations WHERE id = ?");
                $getInfo->execute([$id]);
                $regData = $getInfo->fetch(PDO::FETCH_ASSOC);

                if ($regData) {
                    // Kiểm tra xem đã là thành viên chưa để tránh bị trùng bản ghi
                    $checkMember = $this->db->prepare("SELECT id FROM member WHERE user_id = ? AND club_id = ?");
                    $checkMember->execute([$regData['user_id'], $regData['club_id']]);
                    
                    if ($checkMember->rowCount() == 0) {
                        // Thêm vào bảng member
                        $addMember = $this->db->prepare("INSERT INTO member (user_id, club_id, position, joined_date) VALUES (?, ?, 'Thành viên', CURDATE())");
                        $addMember->execute([$regData['user_id'], $regData['club_id']]);
                    }
                }
            }

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getByUser($userId) {
        $sql = "SELECT r.*, c.club_name 
                FROM club_registrations r
                JOIN clubs c ON r.club_id = c.id
                WHERE r.user_id = ?
                ORDER BY r.registered_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllClubsWithUserStatus($userId) {
        $sql = "SELECT 
                    c.id,
                    c.club_name,
                    COALESCE(u.full_name, 'Đang cập nhật') as leader_name,
                    r.status as registration_status,
                    r.id as registration_id
                FROM clubs c
                LEFT JOIN club_registrations r 
                    ON c.id = r.club_id AND r.user_id = ?
                LEFT JOIN users u ON c.leader_id = u.id
                ORDER BY c.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}