<?php
// src/Services/ProfileService.php

class ProfileService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }
    public function updateProfile($data) {
        // 1. Kiểm tra ID người dùng (Bắt buộc phải có để UPDATE)
        if (empty($data['id'])) {
            return ["status" => "error", "message" => "ID người dùng không hợp lệ"];
        }

        // 2. Kiểm tra tính hợp lệ của full_name
        if (empty($data['full_name']) || strlen(trim($data['full_name'])) < 2) {
            return ["status" => "error", "message" => "Họ tên phải có ít nhất 2 ký tự"];
        }

        // 3. Kiểm tra định dạng email (nếu người dùng nhập vào trường contact)
        if (!empty($data['contact']) && !filter_var($data['contact'], FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "Định dạng email không hợp lệ"];
        }

        // 4. Gọi Repository để thực thi câu lệnh UPDATE vào bảng users
        // Đảm bảo Repository đã có hàm updateUserInfo
        $isUpdated = $this->repository->updateUserInfo($data);

        if ($isUpdated) {
            return ["status" => "success", "message" => "Cập nhật hồ sơ thành công"];
        }

        return ["status" => "error", "message" => "Cập nhật thất bại hoặc dữ liệu không thay đổi"];
    }

    public function getProfile($userId) {
        $user = $this->repository->getById($userId);
        
        if (!$user) {
            return ["status" => "error", "message" => "Không tìm thấy người dùng"];
        }

        // Trả về dữ liệu khớp chính xác với các cột trong Database
        return [
            "status" => "success",
            "data" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "full_name" => $user['full_name'], 
                "email" => $user['email'],
                "role" => $user['role'],
                "created_at" => $user['created_at']
            ]
        ];
    }
}
?>