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
            $this->redirect(basePath() . '/login');
            return;
        }

        $targetUserId = (int) ($_POST['user_id'] ?? 0);
        if ($targetUserId <= 0) {
            $_SESSION['flash_error'] = 'Invalid user selection.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteUserByAdmin($targetUserId, (int) $_SESSION['userId']);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'User removed successfully.'
            : 'Could not remove this user.';

        $this->redirect(basePath() . '/dashboard');
    }

    public function deleteApplication()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/login');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if ($applicationId <= 0) {
            $_SESSION['flash_error'] = 'Invalid application selection.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteApplicationById($applicationId);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'Application removed successfully.'
            : 'Could not remove this application.';

        $this->redirect(basePath() . '/dashboard');
    }

    public function deleteReview()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/login');
            return;
        }

        $reviewId = (int) ($_POST['review_id'] ?? 0);
        if ($reviewId <= 0) {
            $_SESSION['flash_error'] = 'Invalid review selection.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteReviewById($reviewId);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'Review removed successfully.'
            : 'Could not remove this review.';

        $this->redirect(basePath() . '/dashboard');
    }

    public function createCategory()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/login');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['flash_error'] = 'Category name is required.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $created = $this->adminModel->createCategory($name);
        $_SESSION['flash_' . ($created ? 'success' : 'error')] = $created
            ? 'Category created successfully.'
            : 'Could not create category. Name may already exist.';

        $this->redirect(basePath() . '/dashboard');
    }

    public function updateCategory()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/login');
            return;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($categoryId <= 0 || $name === '') {
            $_SESSION['flash_error'] = 'Invalid category update request.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $updated = $this->adminModel->updateCategory($categoryId, $name);
        $_SESSION['flash_' . ($updated ? 'success' : 'error')] = $updated
            ? 'Category updated successfully.'
            : 'Could not update category. Name may already exist.';

        $this->redirect(basePath() . '/dashboard');
    }

    public function deleteCategory()
    {
        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/login');
            return;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $_SESSION['flash_error'] = 'Invalid category selection.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $deleted = $this->adminModel->deleteCategoryById($categoryId);
        $_SESSION['flash_' . ($deleted ? 'success' : 'error')] = $deleted
            ? 'Category deleted successfully.'
            : 'Could not delete category. It may still be used by projects.';

        $this->redirect(basePath() . '/dashboard');
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
