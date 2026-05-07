<?php
class RegistrationService {
    private $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    public function registerToEvent($userId, $eventId) {
        // 1. Kiểm tra xem đã đăng ký chưa để tránh lỗi Unique trong DB
        if ($this->repository->checkExists($userId, $eventId)) {
            return ["status" => "error", "message" => "Bạn đã đăng ký sự kiện này rồi"];
        }

        // 2. Thực hiện đăng ký
        if ($this->repository->create($userId, $eventId)) {
            return ["status" => "success", "message" => "Đăng ký tham gia thành công"];
        }
        return ["status" => "error", "message" => "Lỗi hệ thống, vui lòng thử lại"];
    }

    public function getEventList($eventId) {
        return $this->repository->getMembersByEvent($eventId);
    }
}
?>