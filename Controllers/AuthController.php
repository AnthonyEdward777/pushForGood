<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ==========================================
    // 2. PUBLIC ROUTES
    // ==========================================

    // --- Login Flow ---
    public function showLoginForm($errorMessage = '')
    {
        if (isset($_SESSION['userId'])) {
            $this->redirect(basePath() . '/dashboard');
        }

        $this->render('auth/login', ['errorMessage' => $errorMessage]);
    }

    public function login()
    {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if ($email === '' || $password === '') {
            $this->showLoginForm('Email and password are required.');
            return;
        }

        $loggedInUser = User::login($this->db, $email, $password);
        if (!$loggedInUser) {
            $this->showLoginForm('Invalid email or password.');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['userId'] = $loggedInUser['id'];
        $_SESSION['userName'] = $loggedInUser['name'];
        $_SESSION['userRole'] = $loggedInUser['role'];

        $this->redirect(basePath() . '/dashboard');
    }

    // --- Register Flow ---
    public function showRegisterForm($errorMessage = '')
    {
        // Assuming you also rename the view file to 'register.php'
        $this->render('auth/register', ['errorMessage' => $errorMessage]);
    }

    public function register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $entity = strtolower(trim($_POST['entity'] ?? 'student'));
        $licenseNumber = trim($_POST['licenseNumber'] ?? '');

        // 1. Basic Validation
        if ($name === '' || $email === '' || $password === '') {
            $this->showRegisterForm('Name, email, and password are required.');
            return;
        }

        // Public registration only supports student and NGO accounts.
        if (!in_array($entity, ['student', 'ngo'], true)) {
            $this->showRegisterForm('Please choose a valid account type.');
            return;
        }

        // 2. Pack the data to pass to the Model
        $data = [
            'user_name' => $name,
            'email_address' => $email,
            'user_password' => $password,
            'licenseNumber' => $licenseNumber
        ];

        // 3. POLYMORPHISM: Build the exact specialist we need
        if ($entity === 'ngo') {
            require_once __DIR__ . '/../Models/Ngo.php';
            $model = new NGO($this->db);
        } elseif ($entity === 'admin') {
            require_once __DIR__ . '/../Models/admin.php';
            $model = new Admin($this->db);
        } else {
            // Defaults to Student
            require_once __DIR__ . '/../Models/Student.php';
            $model = new Student($this->db);
        }

        // 4. Ask the specific object to register itself
        $created = $model->register($data);

        // 5. If it failed, pull the error from that specific object
        if (!$created) {
            $this->showRegisterForm($model->getLastError());
            return;
        }

        // 6. Auto-login the user
        $loggedInUser = User::login($this->db, $email, $password);
        if ($loggedInUser) {
            // Security Best Practice: Refresh the session ID
            session_regenerate_id(true);

            // Give the user their "VIP Wristbands"
            $_SESSION['userId'] = $loggedInUser['id'];
            $_SESSION['userName'] = $loggedInUser['name'];
            $_SESSION['userRole'] = $loggedInUser['role'];

            // Drop them directly into the application!
            $this->redirect(basePath() . '/dashboard');
        } else {
            // A safety fallback, just in case something weird happens
            $this->redirect(basePath() . '/login');
        }
    }

    // --- Session Management ---
    public function dashboard()
    {
        // 1. Security Check: Are they actually logged in?
        if (!isset($_SESSION['userId'])) {
            $this->redirect(basePath() . '/login');
            return; // Always return after a redirect to stop script execution
        }

        // 2. Grab their details from the session
        $role = strtolower($_SESSION['userRole'] ?? '');
        $name = $_SESSION['userName'] ?? 'Member';
        $userId = $_SESSION['userId']; // <-- Extract userId to use in our database query

        // 3. Traffic Cop: Route them to their specific interface
        switch ($role) {
            case 'student':
            case 'user':
                require_once __DIR__ . '/../Models/Project.php';
                require_once __DIR__ . '/../Models/Application.php';

                $projectModel = new Project($this->db);
                $applicationModel = new Application($this->db);

                $projectFilters = [
                    'type' => trim($_GET['type'] ?? ''),
                    'duration' => trim($_GET['duration'] ?? ''),
                    'skill' => trim($_GET['skill'] ?? ''),
                ];

                $projects = $projectModel->searchProjects($projectFilters);
                $projectTypes = $projectModel->getProjectTypes();
                $appliedProjectIds = $applicationModel->getAppliedProjectIdsByStudent((int) $userId);
                $studentApplications = $applicationModel->getApplicationsByStudent((int) $userId);

                $this->render('dashboards/student_dashboard', [
                    'name' => $name,
                    'role' => $role,
                    'projects' => $projects,
                    'projectFilters' => $projectFilters,
                    'projectTypes' => $projectTypes,
                    'appliedProjectIds' => $appliedProjectIds,
                    'studentApplications' => $studentApplications,
                    'flashSuccess' => $_SESSION['flash_success'] ?? null,
                    'flashError' => $_SESSION['flash_error'] ?? null,
                ]);

                unset($_SESSION['flash_success'], $_SESSION['flash_error']);
                break;

            case 'ngo':
                // --- ADDED: Fetch NGO Projects ---
                require_once __DIR__ . '/../Models/Project.php';
                $projectModel = new Project($this->db);
                $projects = $projectModel->getProjectsByUserId($userId);
                // ---------------------------------

                $this->render('dashboards/ngo_dashboard', [
                    'name' => $name,
                    'role' => $role,
                    'projects' => $projects // <-- Pass the fetched projects to the view!
                ]);
                break;

            case 'admin':
                require_once __DIR__ . '/../Models/Admin.php';
                $adminModel = new Admin($this->db);

                $this->render('dashboards/admin_dashboard', [
                    'name' => $name,
                    'role' => $role,
                    'users' => $adminModel->getManageableUsers(),
                    'applications' => $adminModel->getAllApplications(),
                    'reviews' => $adminModel->getAllReviews(),
                    'categories' => $adminModel->getAllCategories(),
                    'flashSuccess' => $_SESSION['flash_success'] ?? null,
                    'flashError' => $_SESSION['flash_error'] ?? null,
                ]);

                unset($_SESSION['flash_success'], $_SESSION['flash_error']);
                break;

            default:
                // If they somehow have no role, force them out for security
                $this->redirect(basePath() . '/logout');
                break;
        }
    }
    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirect(basePath() . '/login');
    }

    // --- Admin Authentication ---
    public function showAdminLoginForm($errorMessage = '')
    {
        $this->render('auth/admin_login', ['errorMessage' => $errorMessage]);
    }

    public function adminLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->showAdminLoginForm('Email and password are required.');
            return;
        }

        // Use our static Model function to check the database
        $loggedInUser = User::login($this->db, $email, $password);

        // THE SECURITY WALL: Are they logged in AND are they an Admin?
        if ($loggedInUser && strtolower($loggedInUser['role']) === 'admin') {

            session_regenerate_id(true);

            $_SESSION['userId'] = $loggedInUser['id'];
            $_SESSION['userName'] = $loggedInUser['name'];
            $_SESSION['userRole'] = $loggedInUser['role'];

            // Route them to the main traffic cop, which will load the admin dashboard
            $this->redirect(basePath() . '/dashboard');
        } else {
            // Give a generic error so hackers don't know if the email exists or not
            $this->showAdminLoginForm('Invalid administrator credentials.');
        }
    }

    // ==========================================
    // 3. PRIVATE HELPERS 
    // ==========================================

    private function render($viewPath, $data = [])
    {
        extract($data);
        require __DIR__ . '/../Views/' . $viewPath . '.php';
    }

    private function redirect($location)
    {
        header("Location: $location");
        exit;
    }
}
