<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/ProjectController.php';


$database = new Database();
$db = $database->getConnection();
$authController = new AuthController($db);
$projectController = new ProjectController($db);
$homeController = new HomeController();

$method = $_SERVER['REQUEST_METHOD'];

// 1. Get URL
$request = $_SERVER['REQUEST_URI'];

// 2. Clean URL
$basePath = '/pushforgood';
$path = str_replace($basePath, '', $request);

// 3. Remove any trailing slashes or query strings
$path = parse_url($path, PHP_URL_PATH);
$path = rtrim($path, '/');

// 4. The Modern Switch Router
switch ($path) {

    // Home Route
    case '':
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
        // No POST needed here, it's just a display page
        $projectController->view($resourceId);
        break;

    // -- Admin Routes --
    case '/admin/login':
        // If they click the submit button
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->adminLogin();
        } else {
            // If they are just loading the page
            $authController->showAdminLoginForm();
        }
        break;

    default:
        // Industry Standard 404 Page
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The page you are looking for does not exist.</p>";
        break;
}
