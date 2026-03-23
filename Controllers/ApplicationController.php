<?php
require_once '../database.php';
require_once '../Models/Application.php';

class ApplicationController
{
    private $db;
    private $applicationModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->applicationModel = new Application($this->db);
    }

    public function submitApplication()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'student') {
            die("Unauthorized access.");
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $projectId = $_POST['project_id'];
            $studentId = $_SESSION['id']; 
            $comment = trim($_POST['comment']);
            $filePath = null;

            if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
                
                $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg'];
                $maxSize = 5 * 1024 * 1024;

                $fileType = $_FILES['submission_file']['type'];
                $fileSize = $_FILES['submission_file']['size'];

                if (!in_array($fileType, $allowedTypes)) {
                    die("<script>alert('Error: File must be PDF or JPEG only.'); window.history.back();</script>");
                }

                if ($fileSize > $maxSize) {
                    die("<script>alert('Error: File size must be less than 5MB.'); window.history.back();</script>");
                }

                $targetDir = __DIR__ . "/../public/uploads/";
                
                if (!is_dir($targetDir)) { 
                    mkdir($targetDir, 0777, true); 
                }

                $fileName = time() . "_" . basename($_FILES["submission_file"]["name"]);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["submission_file"]["tmp_name"], $targetFile)) {
                    $filePath = "uploads/" . $fileName;
                }
            }

            $success = $this->applicationModel->submit($projectId, $studentId, $comment, $filePath);

            if ($success) {
                header("Location: /pushforgood/public/dashboard.php?msg=ApplicationSubmitted");
                exit();
            } else {
                $error = $this->applicationModel->getLastError();
                echo "<script>alert('$error');</script>";
            }
        }
    }
}