<?php
include_once __DIR__ . '/../Repositories/RegistrationRepository.php';
class RegistrationController {
    private $repository;
    public function __construct($db) {
        $this->repository = new RegistrationRepository($db);
    }

    public function join() {

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->user_id) || !isset($data->event_id)) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu thông tin đăng ký."
            ]);
            exit;
        }
        $result = $this->repository->register(
            $data->user_id,
            $data->event_id
        );
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

    public function listAll() {

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu user_id"
            ]);
            exit;
        }
        $data = $this->repository->getAllWithDetails($userId);
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
        exit;
    }

    public function updateStatus() {

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"));
        if (
            !isset($data->id) ||
            !isset($data->status) ||
            !isset($data->user_id)
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu dữ liệu cập nhật."
            ]);
            exit;
        }
        $result = $this->repository->updateStatus(
            $data->id,
            $data->status,
            $data->user_id
        );
        if ($result) {
            echo json_encode([
                "status" => "success",
                "message" => "Cập nhật trạng thái thành công!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Bạn không có quyền duyệt đơn này."
            ]);
        }
        exit;
    }

    public function getMyActivities() {

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu ID người dùng."
            ]);
            exit;
        }
        $data = $this->repository->getByUser($userId);
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
        exit;
    }
}