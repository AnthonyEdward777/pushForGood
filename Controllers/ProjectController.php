<?php
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Application.php';
require_once __DIR__ . '/../Models/Review.php';

class ProjectController
{
    private $projectModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->projectModel = new Project($db);
    }

    public function showCreate()
    {
        if ($_SESSION['userRole'] !== 'NGO') {
            $this->redirect(basePath() . '/dashboard');
        }

        $categories = $this->projectModel->getProjectTypes();
        $this->render('projects/create', ['categories' => $categories]);
    }

    public function create()
    {
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($categoryId <= 0) {
            $_SESSION['flash_error'] = 'Please select a valid category.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $this->projectModel->createProject(
            $_SESSION['userId'],
            $categoryId,
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        $this->redirect(basePath() . '/dashboard');
    }

    public function showEdit()
    {
        $id = $_GET['id'] ?? null;

        if (strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $project = $this->projectModel->getProjectByIdAndUser($id, $_SESSION['userId']);

        if (!$project) {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $categories = $this->projectModel->getProjectTypes();
        $this->render('projects/editProject', [
            'project' => $project,
            'categories' => $categories,
        ]);
    }

    public function update()
    {

        $id = $_GET['id'] ?? null;

        if (!$id || strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            $this->redirect(basePath() . '/projects/edit?id=' . $id);
            return;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $this->redirect(basePath() . '/projects/edit?id=' . $id);
            return;
        }

        $this->projectModel->updateProject(
            $id,
            $_SESSION['userId'],
            $categoryId,
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        $this->redirect(basePath() . '/dashboard');
    }
    
    public function view()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $role = strtolower($_SESSION['userRole'] ?? '');
        $applicationModel = new Application($this->db);
        $reviewModel = new Review($this->db);
        $applications = [];
        $alreadyApplied = false;
        $projectReviews = [];
        $canLeaveReview = false;
        $alreadyReviewed = false;

        if ($role === 'ngo') {
            $project = $this->projectModel->getProjectByIdAndUser($id, $_SESSION['userId']);
            if (!$project) {
                $this->redirect(basePath() . '/dashboard?error=not_found');
                return;
            }

            $applications = $applicationModel->getApplicationsByProjectForNgo((int) $id, (int) $_SESSION['userId']);
        } else {
            $project = $this->projectModel->getProjectById($id);
            if ($role === 'student' && isset($_SESSION['userId'])) {
                $alreadyApplied = $applicationModel->hasStudentApplied((int) $id, (int) $_SESSION['userId']);
            }
        }

        if (!$project) {
            $this->redirect(basePath() . '/dashboard?error=not_found');
            return;
        }

        $projectReviews = $reviewModel->getReviewsByProject((int) $id);
        if ($role === 'student' && isset($_SESSION['userId'])) {
            $alreadyReviewed = $reviewModel->hasStudentReviewedProject((int) $id, (int) $_SESSION['userId']);
            $canLeaveReview = $reviewModel->canStudentReviewProject((int) $id, (int) $_SESSION['userId']);
        }

        $this->render('projects/details', [
            'project' => $project,
            'applications' => $applications,
            'alreadyApplied' => $alreadyApplied,
            'projectReviews' => $projectReviews,
            'canLeaveReview' => $canLeaveReview,
            'alreadyReviewed' => $alreadyReviewed,
            'flashSuccess' => $_SESSION['flash_success'] ?? null,
            'flashError' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $this->projectModel->deleteProject($id, $_SESSION['userId']);
        $this->redirect(basePath() . '/dashboard');
    }

    public function list()
    {
        $projects = $this->projectModel->listAllProjects();
        $this->render('projects/list', ['projects' => $projects]);
    }

    public function listAllProjects()
    {
        $projects = $this->projectModel->listAllProjects();
        $this->render('projects/list', ['projects' => $projects]);
    }
    
    private function redirect($url)
    {
        header("Location: /pushforgood" . $url);
        exit();
    }

    private function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../Views/$view.php";
    }
}
