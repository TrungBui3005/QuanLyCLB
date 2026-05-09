<?php
// Lưu ý: Không cần khai báo header lại nếu đã có ở index.php
include_once __DIR__ . '/../Repositories/UserRepository.php';
include_once __DIR__ . '/../Services/UserService.php';

$repository = new UserRepository($db); // Biến $db lấy từ index.php
$service = new UserService($repository);

$method = $_SERVER['REQUEST_METHOD'];

// Xử lý Login
if ($action == 'login' && $method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->username) && isset($data->password)) {
        $user = $service->login($data->username, $data->password);
        if ($user) {
            echo json_encode(["status" => "success", "user" => $user]);
        } else {
            echo json_encode(["status" => "error", "message" => "Sai tài khoản hoặc mật khẩu"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ"]);
    }
    exit; // Dừng lại sau khi trả về kết quả
}

if ($action == 'register' && $method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->username) && isset($data->password) && isset($data->full_name)) {
        $data->role = 'member'; 
        $result = $service->register($data); 
        
        if ($result) {
            $newUserId = $db->lastInsertId();
            $studentCode = $data->student_code;

            $sqlSync = "UPDATE member SET user_id = ? WHERE student_code = ? AND user_id IS NULL";
            $stmtSync = $db->prepare($sqlSync);
            $stmtSync->execute([$newUserId, $studentCode]);
            echo json_encode(["status" => "success", "message" => "Đăng ký thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Tên đăng nhập đã tồn tại"]);
        }
    }
    exit;
}

// Xử lý List
if ($action == 'list' && $method == 'GET') {
    $users = $service->getUserList();
    echo json_encode(["status" => "success", "data" => $users]);
    exit;
}
// Xử lý Cập nhật quyền hạn
if ($action == 'update-role' && $method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->id) && isset($data->role)) {
      
        $club_id = isset($data->club_id) ? $data->club_id : null;
       
        $result = $service->updateUserRole($data->id, $data->role, $club_id);
        
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Cập nhật quyền thành công"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Cập nhật thất bại"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Dữ liệu thiếu ID hoặc Role"]);
    }
    exit;
}
