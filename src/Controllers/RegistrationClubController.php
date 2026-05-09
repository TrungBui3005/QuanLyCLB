<?php
include_once __DIR__ . '/../Repositories/RegistrationClubRepository.php';

class RegistrationClubController {
    private $repository;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->repository = new RegistrationClubRepository($db);
    }

    // Hàm xử lý đăng ký gia nhập CLB
    public function join() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->user_id) || !isset($data->club_id)) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin đăng ký CLB."]);
            exit;
        }

        $reason = $data->reason ?? '';
        $result = $this->repository->register($data->user_id, $data->club_id, $reason);
        
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Gửi đơn gia nhập CLB thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Bạn đã gửi đơn cho CLB này rồi hoặc có lỗi xảy ra."]);
        }
        exit;
    }

    // Lấy danh sách đơn đăng ký (Phân quyền Admin/Chủ nhiệm)
    public function listAll() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        // Lấy user_id từ tham số GET (truyền từ frontend)
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(["status" => "error", "message" => "Không xác định được người dùng."]);
            exit;
        }

        $stmt = $this->db->prepare("SELECT role, club_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentUser) {
            echo json_encode(["status" => "error", "message" => "Người dùng không tồn tại."]);
            exit;
        }

        $managedClubId = ($currentUser['role'] === 'chunhiem') ? $currentUser['club_id'] : null;

        $data = $this->repository->getAllWithDetails($managedClubId);
        
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }

    // Cập nhật trạng thái duyệt đơn (Duyệt / Từ chối)
    public function updateStatus() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id) || !isset($data->status)) {
            echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu cập nhật."]);
            exit;
        }

        $result = $this->repository->updateStatus($data->id, $data->status);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Thao tác thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật trạng thái đơn."]);
        }
        exit;
    }
    public function listForStudent() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(["status" => "error", "message" => "Thiếu user_id"]);
            exit;
        }

        $clubs = $this->repository->getAllClubsWithUserStatus($userId);
        
        echo json_encode([
            "status" => "success", 
            "clubs" => $clubs
        ]);
        exit;
    }
}