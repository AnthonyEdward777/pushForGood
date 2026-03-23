<?php

class Review
{
    private $db;
    private $lastError = '';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function hasStudentReviewedProject($projectId, $studentId)
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM reviews
             WHERE project_id = :project_id
               AND reviewer_id = :reviewer_id
               AND deleted_at IS NULL
             LIMIT 1'
        );

        $stmt->execute([
            ':project_id' => (int) $projectId,
            ':reviewer_id' => (int) $studentId,
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function canStudentReviewProject($projectId, $studentId)
    {
        $stmt = $this->db->prepare(
            'SELECT a.id
             FROM applications a
             WHERE a.project_id = :project_id
               AND a.student_id = :student_id
               AND a.status = "Accepted"
               AND a.deleted_at IS NULL
             LIMIT 1'
        );

        $stmt->execute([
            ':project_id' => (int) $projectId,
            ':student_id' => (int) $studentId,
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createReview($projectId, $studentId, $rating, $comments)
    {
        $this->lastError = '';

        $rating = (int) $rating;
        if ($rating < 1 || $rating > 5) {
            $this->lastError = 'Rating must be between 1 and 5.';
            return false;
        }

        if ($this->hasStudentReviewedProject($projectId, $studentId)) {
            $this->lastError = 'You already reviewed this project.';
            return false;
        }

        if (!$this->canStudentReviewProject($projectId, $studentId)) {
            $this->lastError = 'You can only review projects where your application was accepted.';
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO reviews (project_id, reviewer_id, rating, comments)
                 VALUES (:project_id, :reviewer_id, :rating, :comments)'
            );

            return $stmt->execute([
                ':project_id' => (int) $projectId,
                ':reviewer_id' => (int) $studentId,
                ':rating' => $rating,
                ':comments' => $comments,
            ]);
        } catch (PDOException $exception) {
            error_log('Review submission failed: ' . $exception->getMessage());
            $this->lastError = 'Could not submit review. Please try again.';
            return false;
        }
    }

    public function getReviewsByProject($projectId)
    {
        $stmt = $this->db->prepare(
            'SELECT r.id, r.rating, r.comments, r.created_at,
                    u.user_name AS reviewer_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.reviewer_id
             WHERE r.project_id = :project_id
               AND r.deleted_at IS NULL
             ORDER BY r.created_at DESC'
        );

        $stmt->execute([':project_id' => (int) $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
