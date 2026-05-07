<?php
// src/Controllers/ProfileController.php
include_once __DIR__ . '/../Repositories/ProfileRepository.php';
include_once __DIR__ . '/../Services/ProfileService.php';

// Khởi tạo các lớp đối tượng
// Biến $db phải được khởi tạo từ tệp kết nối CSDL (ví dụ: database.php)
$repository = new ProfileRepository($db); 
$service = new ProfileService($repository);

$method = $_SERVER['REQUEST_METHOD'];
// Biến $action cần được xử lý từ Router hoặc URL (ví dụ: $_GET['action'])

// 1. API Lấy thông tin cá nhân
// URL ví dụ: index.php?action=get&id=1
if ($method == 'GET' && $action == 'get') {
    $userId = $_GET['id'] ?? null;
    if ($userId) {
        // Trả về thông tin từ bảng users (full_name, email, role...)
        $result = $service->getProfile($userId); 
    } else {
        $result = ["status" => "error", "message" => "Thiếu ID người dùng"];
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// 2. API Cập nhật hồ sơ
// URL ví dụ: index.php?action=update
if ($method == 'POST' && $action == 'update') {
    // Nhận dữ liệu JSON gồm: id, full_name, contact (email)[cite: 1]
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($data) {
        // Thực hiện cập nhật qua Service vào bảng users
        $result = $service->updateProfile($data); 
    } else {
        $result = ["status" => "error", "message" => "Dữ liệu không hợp lệ"];
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// 3. API Lấy danh sách thành viên (Nếu vẫn cần dùng cho quản lý)
if ($method == 'GET' && $action == 'list-all') {
    // Trong bảng users mới, chúng ta lấy tất cả người dùng có role là 'member'[cite: 3]
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