<?php
include_once __DIR__ . '/../Repositories/TintucRepository.php';
include_once __DIR__ . '/../Services/TintucService.php';

class TintucController {
    private $service;

    public function __construct($db) {
        $repository = new TintucRepository($db);
        $this->service = new TintucService($repository);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        $result = $this->service->createNews($data);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function list() {
        $news = $this->service->getAllNews();
        header('Content-Type: application/json');
        echo json_encode($news);   // Trả về trực tiếp mảng
        exit;
    }
        public function getById() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
            exit;
        }
        $news = $this->service->getById($id);
        header('Content-Type: application/json');
        echo json_encode($news ? $news : ["status" => "error", "message" => "Không tìm thấy tin tức"]);
        exit;
    }

    public function update() {
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"));
        
        if (!$id || !$data) {
            echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
            exit;
        }

        $result = $this->service->updateNews($id, $data);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Thiếu ID"]);
            exit;
        }

        $result = $this->service->deleteNews($id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
?>