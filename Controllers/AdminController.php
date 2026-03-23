<?php
require_once __DIR__ . '/../Models/Admin.php';

class AdminController
{
    private $adminModel;

    public function __construct($db)
    {
        $this->adminModel = new Admin($db);
    }

    public function deleteUser()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/pushforgood/login');
            return;
        }

        $targetUserId = (int) ($_POST['user_id'] ?? 0);
        if ($targetUserId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user selection.';
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteUserByAdmin($targetUserId, (int) $_SESSION['userId']);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'User removed successfully.'
            : 'Could not remove this user.';

        $this->redirect('/pushforgood/dashboard');
    }

    public function deleteApplication()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/pushforgood/login');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if ($applicationId <= 0) {
            $_SESSION['flash_error'] = 'Invalid application selection.';
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteApplicationById($applicationId);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'Application removed successfully.'
            : 'Could not remove this application.';

        $this->redirect('/pushforgood/dashboard');
    }

    public function deleteReview()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/pushforgood/login');
            return;
        }

        $reviewId = (int) ($_POST['review_id'] ?? 0);
        if ($reviewId <= 0) {
            $_SESSION['flash_error'] = 'Invalid review selection.';
            $this->redirect('/pushforgood/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteReviewById($reviewId);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'Review removed successfully.'
            : 'Could not remove this review.';

        $this->redirect('/pushforgood/dashboard');
    }

    private function isAdmin()
    {
        return isset($_SESSION['userId']) && strtolower($_SESSION['userRole'] ?? '') === 'admin';
    }

    private function redirect($location)
    {
        header("Location: $location");
        exit;
    }
}
