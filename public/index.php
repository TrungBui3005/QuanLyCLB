<?php
ob_start(); 
error_reporting(E_ALL); 
ini_set('display_errors', 1);

// Cấu hình Header CORS & JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// Phân tách URL (Dạng index.php/resource/action)
$path = $_SERVER['PATH_INFO'] ?? '';
$uri = explode('/', trim($path, '/'));

$resource = $uri[0] ?? null; 
$action   = $uri[1] ?? null; 

include_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();

switch ($resource) {
    case 'user':
        include_once __DIR__ . '/../src/Controllers/UserController.php';
        $controller = new UserController($db);
        if ($action == 'login') $controller->login();
        elseif ($action == 'register')
            {
            $controller->register();
            }
         elseif ($action == 'list') {       
        $controller->list();
    } elseif ($action == 'update-role') {
        $controller->updateRole();
    }
        break;

    case 'event':
        include_once __DIR__ . '/../src/Controllers/EventController.php';
        $controller = new EventController($db);
        
        if ($action == 'create') {
            $controller->create();
        } elseif ($action == 'list') {
            $controller->list();
        } elseif ($action == 'get') {
            $controller->detail();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        }
        break;
       case 'tintuc':
        include_once __DIR__ . '/../src/Controllers/TintucController.php';
        $controller = new TintucController($db);
        
        if ($action == 'create') {
            $controller->create();
        } elseif ($action == 'list') {
            $controller->list();
        } elseif ($action == 'get') {
            $controller->getById();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        }
        break;
    case 'club':
        include_once __DIR__ . '/../src/Repositories/ClubRepository.php';
        include_once __DIR__ . '/../src/Services/ClubService.php';
        include_once __DIR__ . '/../src/Controllers/ClubController.php';

        $clubRepo = new ClubRepository($db);
        $clubService = new ClubService($clubRepo);
        $clubController = new ClubController($clubService);

        // Gọi hàm xử lý request của ClubController
        $clubController->handleRequest();
        break;

    case 'registration':
        include_once __DIR__ . '/../src/Controllers/RegistrationController.php';
        $controller = new RegistrationController($db);
        
        if ($action == 'join') {
            $controller->join(); 
        } elseif ($action == 'list-all') {
            $controller->listAll();
        } elseif ($action == 'update-status') {
            $controller->updateStatus();
        } elseif ($action == 'my-activities') {
            $controller->getMyActivities();
        }
        break;
            case 'registrationclub':
        include_once __DIR__ . '/../src/Controllers/RegistrationClubController.php';
        $controller = new RegistrationClubController($db);
        
        if ($action == 'join') {
            $controller->join(); 
        } elseif ($action == 'list-all') {
            $controller->listAll();
        } elseif ($action == 'update-status') {
            $controller->updateStatus();
        } 
        elseif ($action == 'listForStudent') {
            $controller->listForStudent();
        }
        break;

case 'member':
    include_once __DIR__ . '/../src/Controllers/MemberController.php';
    $controller = new MemberController($db);    
    
    if ($action == 'list') {
        $controller->list();
    } elseif ($action == 'get') {
        $controller->get();
    } elseif ($action == 'save') {
        $controller->save();
    } elseif ($action == 'delete') {
        $controller->delete();
    }
    break;
        
        case 'profile':
        include_once __DIR__ . '/../src/Controllers/ProfileController.php';
        $controller = new ProfileController($db);
        
        if ($action == 'update') {
            $controller->update(); // Gọi hàm update trong Controller
        } else {
            echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ"]);
        }
        break;

    default:
        if (ob_get_length()) ob_clean();
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint không tồn tại"]);
        break;
}

ob_end_flush();