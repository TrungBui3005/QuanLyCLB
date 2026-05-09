<?php
class ClubService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public function listClubsByPermission($userId, $role) {
        if ($role === 'admin') {
            return $this->repository->getAll();
        } else {
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
        $leaderId = $data['leader_id'] ?? null;

        return $this->repository->update($data['id'], $name, $desc, $leaderId);
    }

    // Xóa CLB (Chỉ Admin)
    public function deleteClub($id) {
        if (!$id) return false;
        return $this->repository->delete($id);
    }

    public function register($data) {
        if (empty($data['user_id']) || empty($data['club_id'])) {
            return ["status" => "error", "message" => "Thiếu thông tin người dùng hoặc câu lạc bộ!"];
        }
        $success = $this->repository->saveRegistration($data['user_id'], $data['club_id'], $data['reason'] ?? '');
        
        return $success ? 
            ["status" => "success", "message" => "Gửi đơn thành công!"] : 
            ["status" => "error", "message" => "Bạn đã gửi đơn cho CLB này rồi hoặc có lỗi xảy ra."];
    }
}