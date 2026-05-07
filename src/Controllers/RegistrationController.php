<?php
// src/Controllers/RegistrationController.php
include_once __DIR__ . '/../Repositories/RegistrationRepository.php';
class RegistrationController {
    private $repository;
    public function __construct($db) {
        // Khởi tạo repository để dùng trong các hàm bên dưới
        $this->repository = new RegistrationRepository($db);
    }

    public function join() {
        // 1. Dọn dẹp bộ đệm và thiết lập Header JSON
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        // 2. Đọc dữ liệu từ phía Client (register_event.html) gửi lên qua Fetch API
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->user_id) || !isset($data->event_id)) {
            echo json_encode(["status" => "error", "message" => "Thiếu thông tin đăng ký."]);
            exit;
        }

        // 3. Gọi Repository để lưu vào Database
        $result = $this->repository->register($data->user_id, $data->event_id);
        
        if ($result) {
            echo json_encode([
                "status" => "success", 
                "message" => "Đăng ký thành công! Vui lòng chờ duyệt."
            ]);
        } else {
            echo json_encode([
                "status" => "error", 
                "message" => "Bạn đã đăng ký sự kiện này rồi hoặc có lỗi xảy ra."
            ]);
        }
        exit;
    }
    // Hàm lấy danh sách cho trang Admin duyệt đơn
    public function listAll() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $data = $this->repository->getAllWithDetails();
        echo json_encode(["status" => "success", "data" => $data]);
        exit;
    }
    // Hàm cập nhật trạng thái duyệt đơn

    public function updateStatus() {
    // 1. Dọn dẹp bộ đệm và thiết lập Header JSON
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    // 2. Lấy dữ liệu từ Admin gửi lên (thường là id và status mới)
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->status)) {
        echo json_encode(["status" => "error", "message" => "Thiếu ID hoặc trạng thái cập nhật."]);
        exit;
    }

    // 3. Gọi Repository để cập nhật vào Database
    $result = $this->repository->updateStatus($data->id, $data->status);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Cập nhật trạng thái thành công!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Không thể cập nhật trạng thái."
        ]);
    }
    exit;}

    public function getMyActivities() {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $userId = $_GET['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(["status" => "error", "message" => "Thiếu ID người dùng."]);
        exit;
    }

    $data = $this->repository->getByUser($userId);
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}
}