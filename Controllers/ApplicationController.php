<?php
require_once __DIR__ . '/../Models/Application.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Contract.php';

class ApplicationController
{
    private $db;
    private $applicationModel;
    private $projectModel;
    private $contractModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->applicationModel = new Application($db);
        $this->projectModel = new Project($db);
        $this->contractModel = new Contract($db);
    }

    public function apply()
    {
        if (!isset($_SESSION['userId']) || strtolower($_SESSION['userRole'] ?? '') !== 'student') {
            $this->redirect(basePath() . '/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $studentId = (int) $_SESSION['userId'];
        $comment = trim($_POST['comment'] ?? '');
        $filePath = null;

        if ($projectId <= 0) {
            $_SESSION['flash_error'] = 'Invalid project selected.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $project = $this->projectModel->getProjectById($projectId);
        if (!$project || strtolower($project['status'] ?? '') !== 'open') {
            $_SESSION['flash_error'] = 'This project is not available for application.';
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'CV file is required.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024;

        $fileSize = (int) ($_FILES['submission_file']['size'] ?? 0);
        $tmpFile = $_FILES['submission_file']['tmp_name'] ?? '';
        $detectedType = $tmpFile !== '' && is_file($tmpFile) ? mime_content_type($tmpFile) : '';

        if (!in_array($detectedType, $allowedTypes, true)) {
            $_SESSION['flash_error'] = 'File must be PDF or JPEG only.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        if ($fileSize > $maxSize) {
            $_SESSION['flash_error'] = 'File size must be less than 5MB.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        $targetDir = __DIR__ . '/../public/uploads/';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            $_SESSION['flash_error'] = 'Upload directory is not available.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        $originalName = basename($_FILES['submission_file']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $safeExtension = in_array($extension, ['pdf', 'jpg', 'jpeg'], true) ? $extension : 'pdf';
        $fileName = uniqid('cv_', true) . '.' . $safeExtension;
        $targetFile = $targetDir . $fileName;

        if (!move_uploaded_file($tmpFile, $targetFile)) {
            $_SESSION['flash_error'] = 'Could not upload CV file.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        $filePath = 'uploads/' . $fileName;
        $success = $this->applicationModel->submit($projectId, $studentId, $comment, $filePath);

        if ($success) {
            $_SESSION['flash_success'] = 'Application submitted successfully.';
        } else {
            $_SESSION['flash_error'] = $this->applicationModel->getLastError() ?: 'Could not submit your application.';
        }

        $this->redirect(basePath() . '/projects/view?id=' . $projectId);
    }

    public function updateStatus()
    {
        if (!isset($_SESSION['userId']) || strtolower($_SESSION['userRole'] ?? '') !== 'ngo') {
            $this->redirect(basePath() . '/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(basePath() . '/dashboard');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $projectId = (int) ($_POST['project_id'] ?? 0);
        $status = ucfirst(strtolower(trim($_POST['status'] ?? '')));

        if ($applicationId <= 0 || $projectId <= 0 || !in_array($status, ['Accepted', 'Rejected'], true)) {
            $_SESSION['flash_error'] = 'Invalid application status request.';
            $this->redirect(basePath() . '/projects/view?id=' . $projectId);
            return;
        }

        $updated = $this->applicationModel->updateStatusForNgoProject(
            $applicationId,
            $projectId,
            (int) $_SESSION['userId'],
            $status
        );

        if ($updated) {
            $_SESSION['flash_success'] = 'Application status updated to ' . $status . '.';

            if ($status === 'Accepted') {
                $contractReady = $this->contractModel->ensureForAcceptedApplication(
                    $applicationId,
                    $projectId,
                    (int) $_SESSION['userId']
                );

                if ($contractReady) {
                    $_SESSION['flash_success'] .= ' Contract is ready for download.';
                } else {
                    $_SESSION['flash_error'] = 'Application accepted, but contract generation failed. Please verify contracts table exists.';
                }
            }
        } else {
            $_SESSION['flash_error'] = $this->applicationModel->getLastError() ?: 'Could not update application status.';
        }

        $this->redirect(basePath() . '/projects/view?id=' . $projectId);
    }

    private function redirect($location)
    {
        header("Location: $location");
        exit;
    }
}