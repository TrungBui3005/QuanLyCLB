<?php
include_once __DIR__ . '/../Repositories/EventRepository.php';
include_once __DIR__ . '/../Services/EventService.php';

class EventController {
    private $service;

    public function __construct($db) {
        $repository = new EventRepository($db);
        $this->service = new EventService($repository);
    }

    public function create() {
        // Nhận dữ liệu JSON từ JavaScript
        $data = json_decode(file_get_contents("php://input")); 
        
        if ($data) {
            $result = $this->service->createEvent($data);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            echo json_encode(["status" => "error", "message" => "Dữ liệu JSON trống"]);
        }
        exit;
    }

    public function list() {
        $events = $this->service->getAllEvents();
        header('Content-Type: application/json');
        echo json_encode($events);
        exit;
    }
}