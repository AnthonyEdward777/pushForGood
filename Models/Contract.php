<?php

class Contract
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

    public function ensureForAcceptedApplication($applicationId, $projectId, $ngoUserId)
    {
        $this->lastError = '';

        try {
            $existsStmt = $this->conn->prepare(
                'SELECT c.id
                 FROM contracts c
                 INNER JOIN applications a ON a.id = c.application_id
                 INNER JOIN projects p ON p.id = a.project_id
                 INNER JOIN ngos n ON n.id = p.ngo_id
                 WHERE c.application_id = :application_id
                   AND a.project_id = :project_id
                   AND n.user_id = :ngo_user_id
                   AND a.status = "Accepted"
                   AND c.deleted_at IS NULL
                 LIMIT 1'
            );

            $existsStmt->execute([
                ':application_id' => $applicationId,
                ':project_id' => $projectId,
                ':ngo_user_id' => $ngoUserId,
            ]);

            if ($existsStmt->fetch(PDO::FETCH_ASSOC)) {
                return true;
            }

            $contractNumber = 'CT-' . date('Ymd') . '-' . str_pad((string) $applicationId, 6, '0', STR_PAD_LEFT);
            $termsText = 'Volunteer engagement contract generated after NGO acceptance. Terms may be updated by administration if needed.';

            $insertStmt = $this->conn->prepare(
                'INSERT INTO contracts (application_id, contract_number, terms_text)
                 SELECT a.id, :contract_number, :terms_text
                 FROM applications a
                 INNER JOIN projects p ON p.id = a.project_id
                 INNER JOIN ngos n ON n.id = p.ngo_id
                 WHERE a.id = :application_id
                   AND a.project_id = :project_id
                   AND n.user_id = :ngo_user_id
                   AND a.status = "Accepted"
                   AND a.deleted_at IS NULL
                 LIMIT 1'
            );

            $insertStmt->execute([
                ':contract_number' => $contractNumber,
                ':terms_text' => $termsText,
                ':application_id' => $applicationId,
                ':project_id' => $projectId,
                ':ngo_user_id' => $ngoUserId,
            ]);

            return $insertStmt->rowCount() > 0;
        } catch (PDOException $exception) {
            error_log('Contract ensure failed: ' . $exception->getMessage());
            $this->lastError = 'Could not create contract record.';
            return false;
        }
    }

    public function getContractByApplicationForUser($applicationId, $userId, $role)
    {
        $this->lastError = '';

        if (!in_array($role, ['student', 'ngo'], true)) {
            $this->lastError = 'Unauthorized contract access.';
            return false;
        }

        $accessCondition = $role === 'ngo' ? 'n.user_id = :user_id' : 'a.student_id = :user_id';

        try {
            $stmt = $this->conn->prepare(
                'SELECT c.contract_number, c.created_at AS contract_created_at,
                        a.id AS application_id, a.applied_at, a.status,
                        p.id AS project_id, p.title AS project_title, p.deadline AS project_deadline,
                        student.user_name AS student_name, student.email_address AS student_email,
                        ngo_user.user_name AS ngo_name, ngo_user.email_address AS ngo_email
                 FROM contracts c
                 INNER JOIN applications a ON a.id = c.application_id
                 INNER JOIN projects p ON p.id = a.project_id
                 INNER JOIN ngos n ON n.id = p.ngo_id
                 INNER JOIN users student ON student.id = a.student_id
                 INNER JOIN users ngo_user ON ngo_user.id = n.user_id
                 WHERE c.application_id = :application_id
                   AND c.deleted_at IS NULL
                   AND a.deleted_at IS NULL
                   AND p.deleted_at IS NULL
                   AND a.status = "Accepted"
                   AND ' . $accessCondition . '
                 LIMIT 1'
            );

            $stmt->execute([
                ':application_id' => $applicationId,
                ':user_id' => $userId,
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $this->lastError = 'Contract not found.';
                return false;
            }

            return $row;
        } catch (PDOException $exception) {
            error_log('Contract fetch failed: ' . $exception->getMessage());
            $this->lastError = 'Could not load contract details.';
            return false;
        }
    }
}
