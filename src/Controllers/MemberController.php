<?php
include_once __DIR__ . '/../Repositories/MemberRepository.php';
include_once __DIR__ . '/../Services/MemberService.php';

class MemberController {
    private $service;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $repository = new MemberRepository($db);
        $this->service = new MemberService($repository);
    }

    public function list() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $search = $_GET['search'] ?? '';
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            echo json_encode([]);
            exit;
        }

        // Lấy quyền và club_id của user từ Database để bảo mật
        $stmt = $this->db->prepare("SELECT role, club_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode([]);
            exit;
        }

        // Nếu là chủ nhiệm, bắt buộc lọc theo club_id của họ. Nếu là Admin thì có thể để null để lấy tất cả.
        $managedClubId = ($user['role'] === 'chunhiem') ? $user['club_id'] : null;

        $members = $this->service->getAllMembers($search, $managedClubId);

        echo json_encode($members);
        exit;
    }

    public function get() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id = $_GET['id'] ?? 0;
        $member = $this->service->getMemberById($id);

        echo json_encode($member);
        exit;
    }

    public function save() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents("php://input")); 
        
        if ($data) {
        
            $result = $this->service->saveMember($data);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => "error", "message" => "Dữ liệu JSON không hợp lệ"]);
        }
        exit;
    }

    public function delete() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id = $_GET['id'] ?? 0;
        $result = $this->service->deleteMember($id);

        echo json_encode($result);
        exit;
    }
}