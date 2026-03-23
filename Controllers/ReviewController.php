<?php
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Review.php';

class ReviewController
{
    private $projectModel;
    private $reviewModel;

    public function __construct($db)
    {
        $this->projectModel = new Project($db);
        $this->reviewModel = new Review($db);
    }

    public function create()
    {
        if (!isset($_SESSION['userId']) || strtolower($_SESSION['userRole'] ?? '') !== 'student') {
            $this->redirect('/pushforgood/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comments = trim($_POST['comments'] ?? '');
        $studentId = (int) $_SESSION['userId'];

        if ($projectId <= 0) {
            $_SESSION['flash_error'] = 'Invalid project selected for review.';
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $project = $this->projectModel->getProjectById($projectId);
        if (!$project) {
            $_SESSION['flash_error'] = 'Project not found.';
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $created = $this->reviewModel->createReview($projectId, $studentId, $rating, $comments);
        if ($created) {
            $_SESSION['flash_success'] = 'Thank you, your review has been submitted.';
        } else {
            $_SESSION['flash_error'] = $this->reviewModel->getLastError() ?: 'Could not submit review.';
        }

        $this->redirect('/pushforgood/projects/view?id=' . $projectId);
    }

    private function redirect($location)
    {
        header("Location: $location");
        exit;
    }
}
