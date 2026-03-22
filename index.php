<?php
session_start();
require_once __DIR__ . '/Controllers/AuthController.php';

$authController = new AuthController();
$page = $_GET['page'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($page === 'login') {
    if ($method === 'POST') {
        $authController->login();
    }
    $authController->showLogin();
} elseif ($page === 'signup' || $page === 'register') {
    if ($method === 'POST') {
        $authController->signup();
    }
    $authController->showSignup();
} elseif ($page === 'dashboard' || $page === 'adminDashboard' || $page === 'ngoDashboard' || $page === 'userDashboard') {
    $authController->dashboard();
} elseif ($page === 'logout') {
    $authController->logout();
} elseif ($page === 'regSuccess') {
    require __DIR__ . '/Views/auth/regSuccess.php';
} else {
    require __DIR__ . '/Views/home.php';
}
