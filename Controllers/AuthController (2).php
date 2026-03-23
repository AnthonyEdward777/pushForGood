<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    private function render($viewPath, $data = [])
    {
        extract($data);
        require __DIR__ . '/../Views/' . $viewPath . '.php';
    }

    private function redirect($location)
    {
        header('Location: ' . $location);
        exit();
    }

    public function showLogin($errorMessage = '')
    {
        if (isset($_SESSION['userId'])) {
            $this->redirect('index.php?page=dashboard');
        }

        $this->render('auth/login', [
            'errorMessage' => $errorMessage,
        ]);
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->showLogin('Email and password are required.');
            return;
        }

        $loggedInUser = $this->userModel->login($email, $password);
        if (!$loggedInUser) {
            $this->showLogin('Invalid email or password.');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['userId'] = $loggedInUser['id'];
        $_SESSION['userName'] = $loggedInUser['name'];
        $_SESSION['userRole'] = $loggedInUser['role'];

        $this->redirect('views/dashboard.php');
    }

    public function showSignup($errorMessage = '')
    {
        $this->render('auth/signup', [
            'errorMessage' => $errorMessage,
        ]);
    }

    public function signup()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $entity = strtolower(trim($_POST['entity'] ?? 'user'));
        $licenseNumber = trim($_POST['licenseNumber'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->showSignup('Name, email, and password are required.');
            return;
        }

        if (!in_array($entity, ['admin', 'user', 'ngo'], true)) {
            $this->showSignup('Please choose a valid account type.');
            return;
        }

        if ($entity === 'ngo' && $licenseNumber === '') {
            $this->showSignup('NGO license number is required for NGO accounts.');
            return;
        }

        $created = $this->userModel->register($name, $email, $password, $entity, $licenseNumber);
        if (!$created) {
            $this->showSignup($this->userModel->getLastError());
            return;
        }

        $this->redirect('index.php?page=regSuccess');
    }

    public function dashboard()
    {
        if (!isset($_SESSION['userId'])) {
            $this->redirect('index.php?page=login');
        }

        $role = $_SESSION['userRole'] ?? 'User';
        $name = $_SESSION['userName'] ?? 'Member';

        $dashboardText = 'Welcome to your dashboard.';
        if (strcasecmp($role, 'Admin') === 0) {
            $dashboardText = 'Admin dashboard: manage users, approvals, and platform content.';
        } elseif (strcasecmp($role, 'NGO') === 0) {
            $dashboardText = 'NGO dashboard: post opportunities and manage applications.';
        } else {
            $dashboardText = 'User dashboard: explore opportunities and track your applications.';
        }

        $this->render('auth/dashboard', [
            'role' => $role,
            'name' => $name,
            'dashboardText' => $dashboardText,
        ]);
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        $this->redirect('index.php?page=login');
    }
}
