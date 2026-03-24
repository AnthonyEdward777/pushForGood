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

    public function hasStudentApplied($projectId, $studentId)
    {
        $stmt = $this->conn->prepare(
            'SELECT id
             FROM applications
             WHERE project_id = :project_id
               AND student_id = :student_id
               AND deleted_at IS NULL
             LIMIT 1'
        );

        $stmt->execute([
            ':project_id' => $projectId,
            ':student_id' => $studentId,
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function submit($projectId, $studentId, $comment, $filePath)
    {
        $this->lastError = '';

        if ($this->hasStudentApplied($projectId, $studentId)) {
            $this->lastError = 'You already applied to this project.';
            return false;
        }

        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO applications (project_id, student_id, comment, file_path, status)
                 VALUES (:project_id, :student_id, :comment, :file_path, :status)'
            );
    
            return $stmt->execute([
                ':project_id' => $projectId,
                ':student_id' => $studentId,
                ':comment' => $comment,
                ':file_path' => $filePath,
                ':status'      => 'Pending'
            ]);
        } catch (PDOException $exception) {
            error_log('Application submission failed: ' . $exception->getMessage());
            if ($exception->getCode() === '42S22') {
                $this->lastError = 'Database is missing application form columns. Please run schema update for comment and file_path.';
            } else {
                $this->lastError = 'Submission failed. Please try again.';
            }
            return false;
        }
    }

    public function getApplicationsByProjectForNgo($projectId, $ngoUserId)
    {
        $stmt = $this->conn->prepare(
            'SELECT a.*, u.user_name AS student_name, u.email_address AS student_email,
                                        c.id AS contract_id, c.contract_number
             FROM applications a
             INNER JOIN projects p ON p.id = a.project_id
             INNER JOIN ngos n ON n.id = p.ngo_id
             INNER JOIN users u ON u.id = a.student_id
                         LEFT JOIN contracts c ON c.application_id = a.id AND c.deleted_at IS NULL
             WHERE a.project_id = :project_id
               AND n.user_id = :ngo_user_id
               AND a.deleted_at IS NULL
             ORDER BY a.created_at DESC'
        );

        $stmt->execute([
            ':project_id' => $projectId,
            ':ngo_user_id' => $ngoUserId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppliedProjectIdsByStudent($studentId)
    {
        $stmt = $this->conn->prepare(
            'SELECT project_id
             FROM applications
             WHERE student_id = :student_id
               AND deleted_at IS NULL'
        );

        $stmt->execute([':student_id' => $studentId]);

        return array_map(
            'intval',
            $stmt->fetchAll(PDO::FETCH_COLUMN)
        );
    }

    public function getApplicationsByStudent($studentId)
    {
        $stmt = $this->conn->prepare(
            'SELECT a.*, p.title AS project_title, p.deadline AS project_deadline, u.user_name AS ngo_name,
                                        c.id AS contract_id, c.contract_number
             FROM applications a
             INNER JOIN projects p ON p.id = a.project_id
             INNER JOIN ngos n ON n.id = p.ngo_id
             INNER JOIN users u ON u.id = n.user_id
                         LEFT JOIN contracts c ON c.application_id = a.id AND c.deleted_at IS NULL
             WHERE a.student_id = :student_id
               AND a.deleted_at IS NULL
               AND p.deleted_at IS NULL
             ORDER BY a.created_at DESC'
        );

        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function updateStatusForNgoProject($applicationId, $projectId, $ngoUserId, $newStatus)
    {
        $this->lastError = '';

        try {
            $stmt = $this->conn->prepare(
                'UPDATE applications a
                 INNER JOIN projects p ON p.id = a.project_id
                 INNER JOIN ngos n ON n.id = p.ngo_id
                 SET a.status = :status
                 WHERE a.id = :application_id
                   AND a.project_id = :project_id
                   AND n.user_id = :ngo_user_id'
            );

            $stmt->execute([
                ':status' => $newStatus,
                ':application_id' => $applicationId,
                ':project_id' => $projectId,
                ':ngo_user_id' => $ngoUserId,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $exception) {
            error_log('Application status update failed: ' . $exception->getMessage());
            $this->lastError = 'Status update failed. Please try again.';
            return false;
        }
    }
}