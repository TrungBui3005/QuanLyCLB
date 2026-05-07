<?php
class ClubService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    /**
     * Lấy danh sách CLB
     * Nếu là Admin: Lấy tất cả
     * Nếu là Chủ nhiệm: Chỉ lấy thông tin CLB mình quản lý
     */
    public function listClubsByPermission($userId, $role) {
        if ($role === 'admin') {
            return $this->repository->getAll();
        } else {
            // Đối với chủ nhiệm hoặc member, có thể cần lấy thông tin CLB cụ thể
            // Giả sử repository có hàm getByLeaderId hoặc bạn có thể lọc tại đây
            return $this->repository->getClubsByUserId($userId);
        }
    }

    // Lấy tất cả CLB (Dùng cho trang Khám phá CLB của Member)
    public function listAllClubs() {
        return $this->repository->getAll();
    }

    // Thêm CLB mới (Chỉ Admin)
    public function addNewClub($data) {
        $name = $data['club_name'] ?? '';
        $desc = $data['description'] ?? '';
        $leader = $data['leader_id'] ?? null;
        
        if (empty($name)) return false;
        
        return $this->repository->create($name, $desc, $leader);
    }
    
    // Cập nhật thông tin CLB
    public function updateClub($data) {
        if (!isset($data['id'])) return false;

        $name = $data['club_name'] ?? '';
        $desc = $data['description'] ?? '';
        // Có thể bổ sung cập nhật leader_id tại đây nếu cần
        $leaderId = $data['leader_id'] ?? null;

        return $this->repository->update($data['id'], $name, $desc, $leaderId);
    }

    // Xóa CLB (Chỉ Admin)
    public function deleteClub($id) {
        if (!$id) return false;
        return $this->repository->delete($id);
    }

    /**
     * Đăng ký gia nhập CLB
     */
    public function register($data) {
        // Logic: Không cho đăng ký nếu thiếu dữ liệu
        if (empty($data['user_id']) || empty($data['club_id'])) {
            return ["status" => "error", "message" => "Thiếu thông tin người dùng hoặc câu lạc bộ!"];
        }
        
        // Gọi repository để lưu đơn đăng ký
        // Lưu ý: Đảm bảo Repository của bạn có hàm saveRegistration hoặc register
        $success = $this->repository->saveRegistration($data['user_id'], $data['club_id'], $data['reason'] ?? '');
        
        return $success ? 
            ["status" => "success", "message" => "Gửi đơn thành công!"] : 
            ["status" => "error", "message" => "Bạn đã gửi đơn cho CLB này rồi hoặc có lỗi xảy ra."];
    }
}