<?php

class MemberRepository {
    private $conn;
    private $table_name = "member";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Lấy danh sách thành viên
     * @param string $search Từ khóa tìm kiếm (tên hoặc mã SV)
     * @param int|null $club_id ID của CLB (Bắt buộc truyền vào nếu là Chủ nhiệm)
     */
   public function getAll($search = '', $club_id = null) {
    $query = "SELECT m.*, 
                     COALESCE(u.full_name, 'Thành viên chưa tạo TK') as full_name, 
                     c.club_name 
              FROM member m
              -- JOIN kép: Ưu tiên ID, nếu ID null thì khớp bằng mã SV
              LEFT JOIN users u ON (m.user_id = u.id OR m.student_code = u.student_code)
              LEFT JOIN clubs c ON m.club_id = c.id
              WHERE 1=1";
    
    if ($club_id !== null) {
        $query .= " AND m.club_id = :club_id";
    }

        // Nếu có từ khóa tìm kiếm
        if (!empty($search)) {
            $query .= " AND (u.full_name LIKE :search OR m.student_code LIKE :search)";
        }

        // ĐIỀU KIỆN QUAN TRỌNG: Lọc theo CLB mà Chủ nhiệm quản lý
        if ($club_id !== null) {
            $query .= " AND m.club_id = :club_id";
        }

        $query .= " ORDER BY m.joined_date DESC";
        
        $stmt = $this->conn->prepare($query);

        // Bind tham số search nếu có
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm);
        }
        
        // Bind club_id nếu có
        if ($club_id !== null) {
            $stmt->bindParam(':club_id', $club_id);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm thành viên theo ID
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lưu thành viên (Thêm mới hoặc Cập nhật)
     */
    public function save($data) {
        // Chuyển $data thành mảng nếu nó đang là object để dễ truy xuất
        $d = (array)$data;

        if (isset($d['id']) && !empty($d['id'])) {
            // CẬP NHẬT THÀNH VIÊN
            $query = "UPDATE " . $this->table_name . " 
                      SET club_id = :club_id, 
                          student_code = :student_code, 
                          department = :department, 
                          position = :position, 
                          joined_date = :joined_date 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':club_id'      => $d['club_id'],
                ':student_code' => $d['student_code'],
                ':department'   => $d['department'],
                ':position'     => $d['position'],
                ':joined_date'  => $d['joined_date'],
                ':id'           => $d['id']
            ]);
        } else {
            // THÊM MỚI THÀNH VIÊN
            // Khi thêm mới, chúng ta cần tìm user_id dựa trên student_code (nếu có)
            // Hoặc để null nếu đây là thành viên ảo chưa có tài khoản
            $userId = $d['user_id'] ?? null;
            
            // Nếu không có user_id nhưng có student_code, hãy thử tìm user_id từ bảng users
            if (!$userId && !empty($d['student_code'])) {
                $checkUser = $this->conn->prepare("SELECT id FROM users WHERE student_code = ? LIMIT 1");
                $checkUser->execute([$d['student_code']]);
                $user = $checkUser->fetch(PDO::FETCH_ASSOC);
                if ($user) $userId = $user['id'];
            }

            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, club_id, student_code, department, position, joined_date) 
                      VALUES (:user_id, :club_id, :student_code, :department, :position, :joined_date)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':user_id'      => $userId,
                ':club_id'      => $d['club_id'],
                ':student_code' => $d['student_code'],
                ':department'   => $d['department'],
                ':position'     => $d['position'],
                ':joined_date'  => $d['joined_date']
            ]);
        }
    }

    /**
     * Xóa thành viên
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}