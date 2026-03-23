<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/ProjectController.php';
require_once __DIR__ . '/Controllers/ApplicationController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/ReviewController.php';

$database = new Database();
$db = $database->getConnection();
$authController = new AuthController($db);
$projectController = new ProjectController($db);
$homeController = new HomeController();
$applicationController = new ApplicationController($db);
$adminController = new AdminController($db);
$reviewController = new ReviewController($db);

$method = $_SERVER['REQUEST_METHOD'];

// Clean URL Handling
$request = $_SERVER['REQUEST_URI'];

$basePath = '/pushforgood';
$path = str_ireplace($basePath, '', $request);

$path = parse_url($path, PHP_URL_PATH);
$path = rtrim($path, '/');

// Switch Router
switch ($path) {

    // Home Route
    case '':
        $homeController->index();
        break;
    case '/':
        $homeController->index();
        break;

    // Auth Routes
    case '/login':
        if ($method === 'POST') {
            $authController->login();
        } else {
            $authController->showLoginForm();
        }
        break;

    case '/register':
        if ($method === 'POST') {
            $authController->register();
        } else {
            $authController->showRegisterForm();
        }
        break;

    case '/dashboard':
        $authController->dashboard();
        break;

    case '/logout':
        $authController->logout();
        break;

    // Project Routes (NGO)
    case '/projects/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectController->create();
        } else {
            $projectController->showCreate();
        }
        break;

    case '/projects/edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectController->update();
        } else {
            $projectController->showEdit();
        }
        break;

    case '/projects/delete':
        $projectController->delete();
        break;

    case '/projects/view':
        $projectController->view();
        break;

    case '/projects/apply':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $applicationController->apply();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;

    // Application Routes 
    case '/applications/update-status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $applicationController->updateStatus();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;
    // Review Routes
    case '/reviews/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewController->create();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;

    // -- Admin Routes --
    case '/admin/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->adminLogin();
        } else {
            $authController->showAdminLoginForm();
        }
        break;

    case '/admin/users/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->deleteUser();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;

    case '/admin/applications/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->deleteApplication();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;

    case '/admin/reviews/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminController->deleteReview();
        } else {
            http_response_code(405);
            echo '<h1>405 Method Not Allowed</h1>';
        }
        break;

    // Default 404 for unmatched routes
    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The page you are looking for does not exist.</p>";
        break;
}
