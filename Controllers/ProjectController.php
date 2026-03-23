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

    // --- ACTION METHODS (POST/GET) ---


    public function showCreate()
    {
        // Only NGOs can see this page
        if ($_SESSION['userRole'] !== 'NGO') {
            $this->redirect('/dashboard');
        }
        // Assuming your file is in Views/projects/create.php
        $this->render('projects/create');
    }

    public function create()
    {
        $this->projectModel->createProject(
            $_SESSION['userId'],
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        // Clean success redirect
        $this->redirect('/dashboard');
    }

    public function showEdit()
    {
        // We grab it here instead
        $id = $_GET['id'] ?? null;

        if (strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $project = $this->projectModel->getProjectByIdAndUser($id, $_SESSION['userId']);

        if (!$project) {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $this->render('projects/editProject', ['project' => $project]);
    }

    // Notice the empty parentheses!
    public function update()
    {
        // 1. Grab the ID directly from the URL (e.g., ?id=4)
        $id = $_GET['id'] ?? null;

        // Security check: Make sure ID exists and they are an NGO
        if (!$id || strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // 2. Make sure the form isn't empty
        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            // FIXED: We use the Query String format here so it doesn't 404
            $this->redirect('/pushforgood/projects/edit?id=' . $id);
            return;
        }

        // 3. Run the secure update
        $this->projectModel->updateProject(
            $id,
            $_SESSION['userId'],
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        // Success! Back to the dashboard
        $this->redirect('/pushforgood/dashboard');
    }
    
    public function view()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/dashboard');
            return;
        }

        $project = $this->projectModel->getProjectsByUserId($id);

        if (!$project) {
            $this->redirect('/dashboard?error=not_found');
            return;
        }

        // We pass the project data to the view
        $this->render('projects/details', ['project' => $project]);
    }

    public function delete()
    {
        // 1. Grab the ID from the query string
        $id = $_GET['id'] ?? null;

        // 2. Security Checks
        if (!$id || strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // 3. Run the secure delete
        $this->projectModel->deleteProject($id, $_SESSION['userId']);
        $this->redirect('/pushforgood/dashboard');
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
