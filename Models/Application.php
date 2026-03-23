<?php

class Application
{
    private $conn;
    private $lastError = '';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function submit($projectId, $studentId, $comment, $filePath)
    {
        $this->lastError = '';

        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO applications (project_id, student_id, comment, file_path, status)
                VALUES (:project_id, :student_id, :comment, :file_path, :status)'
            );
    
            return $stmt->execute([
                ':project_id' => $projectId,
                ':student_id' => $studentId,
                ':comment'     => $comment,
                ':file_path'   => $filePath,
                ':status'      => 'Pending'
            ]);
        } catch (PDOException $exception) {
            error_log('Application submission failed: ' . $exception->getMessage());
            $this->lastError = 'Submission failed. Please try again.';
            return false;
        }
    }

    public function updateStatus($applicationId, $newStatus)
    {
        $this->lastError = '';

        try {
            $stmt = $this->conn->prepare(
                'UPDATE applications SET status = :status WHERE id = :id'
            );
    
            return $stmt->execute([
                ':status' => $newStatus,
                ':id'     => $applicationId
            ]);
        } catch (PDOException $exception) {
            error_log('Application status update failed: ' . $exception->getMessage());
            $this->lastError = 'Status update failed. Please try again.';
            return false;
        }
    }
}