<?php
// Controllers/ProjectController.php

// No session_start() here! It's already in index.php
require_once __DIR__ . '/../Models/Project.php';

class ProjectController
{
    private $projectModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->projectModel = new Project($db);
    }

    // --- VIEW METHODS ---

    public function showCreate()
    {
        // Only NGOs can see this page
        if ($_SESSION['userRole'] !== 'ngo') {
            $this->redirect('/dashboard');
        }
        // Assuming your file is in Views/projects/create.php
        $this->render('projects/create');
    }

    public function showEdit()
    {
        $id = $_GET['id'] ?? null;

        // Use the model to get the data
        $projectData = $this->projectModel->getProjectById($id);

        // SECURITY: If project doesn't exist OR doesn't belong to this NGO, kick them out
        if (!$projectData || $projectData['ngo_id'] != $_SESSION['userId']) {
            $this->redirect('/dashboard?error=unauthorized');
        }

        // Hand the $projectData to the view
        $this->render('projects/edit', ['data' => $projectData]);
    }

    // --- ACTION METHODS (POST/GET) ---

    public function create()
    {
        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            $this->redirect('/projects/create?error=missing_fields');
        }

        $result = $this->projectModel->createProject(
            $_SESSION['userId'], // Fixed session key to match AuthController
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        $this->redirect($result ? '/dashboard?success=created' : '/projects/create?error=failed');
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id || empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            $this->redirect('/dashboard?error=missing_fields');
        }

        $projectData = $this->projectModel->getProjectById($id);
        if (!$projectData || $projectData['ngo_id'] != $_SESSION['userId']) {
            $this->redirect('/dashboard?error=unauthorized');
        }

        $result = $this->projectModel->updateProject(
            $id,
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        $this->redirect($result ? '/dashboard?success=updated' : '/projects/edit?id=' . urlencode((string) $id) . '&error=failed');
    }

    public function view($id)
    {
        if (!$id) $this->redirect('/dashboard');

        $project = $this->projectModel->getProjectById($id);

        if (!$project) {
            $this->redirect('/dashboard?error=not_found');
        }

        // We pass the project data to the view
        $this->render('projects/details', ['project' => $project]);
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        $projectData = $this->projectModel->getProjectById($id);

        if ($projectData && $projectData['ngo_id'] == $_SESSION['userId']) {
            $this->projectModel->deleteProject($id);
            $this->redirect('/dashboard?success=deleted');
        } else {
            $this->redirect('/dashboard?error=unauthorized');
        }
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
