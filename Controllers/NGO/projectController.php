<?php
session_start();


require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood-main/Models/Project.php';

class ProjectController {
    private $project;

    public function __construct() {
        $this->project = new Project();
    }

    public function create() {
        // to validat all the required fields are filled in before creating a project
        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            header("Location: ../../Views/dashboard.php?error=missing_fields");
            exit();
        }

        $result = $this->project->createProject(
            $_SESSION['user_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );
        
        if ($result) {
            header("Location: ../../Views/dashboard.php?success=created");
        } else {
            header("Location: ../../Views/createProject.php?error=failed");
        }
        exit();
    }

    public function delete() {
        // to check if the project belongs to the NGO that is currently logged in before deleting
        $projectData = $this->project->getProjectById($_GET['id']);
        if ($projectData && $projectData['ngo_id'] == $_SESSION['user_id']) {
            $this->project->deleteProject($_GET['id']);
        }
        header("Location: ../../Views/dashboard.php");
        exit();
    }

    public function update() {
        // Security: Check ownership
        $projectData = $this->project->getProjectById($_POST['id']);
        if (!$projectData || $projectData['ngo_id'] != $_SESSION['user_id']) {
            header("Location: ../../Views/dashboard.php?error=unauthorized");
            exit();
        }

        $result = $this->project->updateProject(
            $_POST['id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );
        
        if ($result) {
            header("Location: ../../Views/dashboard.php?success=updated");
        } else {
            header("Location: ../../Views/editProject.php?id=" . $_POST['id'] . "&error=failed");
        }
        exit();
    }
}

// Router
$controller = new ProjectController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header("Location: ../../Views/dashboard.php?error=invalid_action");
        exit();
    }
} else {
    header("Location: ../../Views/dashboard.php");
    exit();
}
?>