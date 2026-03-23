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

    public function showEdit($id)
    {
        if (strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // Fetch the data to pre-fill the form
        $project = $this->projectModel->getProjectByIdAndUser($id, $_SESSION['userId']);

        // If it doesn't exist or isn't theirs, kick them out
        if (!$project) {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // Render the view and pass the data! Matches your 'editProject.php' file
        $this->render('projects/editProject', ['project' => $project]);
    }

    public function update($id)
    {
        if (strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // Make sure the form isn't empty
        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['deadline'])) {
            $this->redirect('/pushforgood/projects/edit/' . $id);
            return;
        }

        // Run the secure update
        $this->projectModel->updateProject(
            $id,
            $_SESSION['userId'],
            $_POST['title'],
            $_POST['description'],
            $_POST['skills'] ?? '',
            $_POST['location'] ?? '',
            $_POST['deadline']
        );

        $this->redirect('/pushforgood/dashboard');
    }
    public function view($id)
    {
        if (!$id) $this->redirect('/dashboard');

        $project = $this->projectModel->getProjectsByUserId($id);

        if (!$project) {
            $this->redirect('/dashboard?error=not_found');
        }

        // We pass the project data to the view
        $this->render('projects/details', ['project' => $project]);
    }

    public function delete($id)
    {
        // Security check
        if (strtolower($_SESSION['userRole']) !== 'ngo') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        // Run the secure delete
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
