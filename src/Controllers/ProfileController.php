<?php
include_once __DIR__ . '/../Repositories/ProfileRepository.php';
include_once __DIR__ . '/../Services/ProfileService.php';

$repository = new ProfileRepository($db); 
$service = new ProfileService($repository);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET' && $action == 'get') {
    $userId = $_GET['id'] ?? null;
    if ($userId) {
       
        $result = $service->getProfile($userId); 
    } else {
        $result = ["status" => "error", "message" => "Thiếu ID người dùng"];
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method == 'POST' && $action == 'update') {
   
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($data) {
     
        $result = $service->updateProfile($data); 
    } else {
        $result = ["status" => "error", "message" => "Dữ liệu không hợp lệ"];
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method == 'GET' && $action == 'list-all') {

    try {
        $query = "SELECT id, username, full_name, email, role FROM users WHERE role = 'member'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $members]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}
?>