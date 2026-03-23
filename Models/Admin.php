<?php
// Models/admin.php
require_once 'User.php';
require_once 'DashboardUI.php';

class Admin extends User implements DashboardUI
{
	public function register($data)
	{
		$this->lastError = '';
		$roleId = $this->getRoleId('admin');

		try {
			$hashedPassword = password_hash($data['user_password'], PASSWORD_DEFAULT);

			$stmt = $this->conn->prepare(
				'INSERT INTO users (user_type_id, user_name, email_address, user_password)
				VALUES (:roleId, :user_name, :email_address, :user_password)'
			);

			return $stmt->execute([
				':roleId' => $roleId,
				':user_name' => $data['user_name'],
				':email_address' => $data['email_address'],
				':user_password' => $hashedPassword,
			]);
		} catch (PDOException $exception) {
			if ($exception->getCode() === '23000') {
				$this->lastError = 'That email is already registered.';
			} else {
				error_log('Admin registration failed: ' . $exception->getMessage());
				$this->lastError = 'Registration failed. Please try again.';
			}
			return false;
		}
	}

	public function generateDashboard()
	{
		return 'dashboards/admin_dashboard';
	}

	public function getManageableUsers()
	{
		$stmt = $this->conn->prepare(
			'SELECT u.id, u.user_name, u.email_address, t.type_name AS role_name, u.created_at
			 FROM users u
			 INNER JOIN user_types t ON t.id = u.user_type_id
			 WHERE LOWER(t.type_name) IN ("student", "ngo")
			   AND u.deleted_at IS NULL
			 ORDER BY u.created_at DESC'
		);

		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function deleteUserByAdmin($targetUserId, $adminUserId)
	{
		$stmt = $this->conn->prepare(
			'DELETE u
			 FROM users u
			 INNER JOIN user_types t ON t.id = u.user_type_id
			 WHERE u.id = :target_id
			   AND u.id <> :admin_id
			   AND LOWER(t.type_name) IN ("student", "ngo")'
		);

		$stmt->execute([
			':target_id' => (int) $targetUserId,
			':admin_id' => (int) $adminUserId,
		]);

		return $stmt->rowCount() > 0;
	}

	public function getAllApplications()
	{
		$stmt = $this->conn->prepare(
			'SELECT a.id, a.status, a.applied_at, a.created_at,
			        p.id AS project_id, p.title AS project_title,
			        u.id AS student_id, u.user_name AS student_name, u.email_address AS student_email
			 FROM applications a
			 INNER JOIN projects p ON p.id = a.project_id
			 INNER JOIN users u ON u.id = a.student_id
			 WHERE a.deleted_at IS NULL
			 ORDER BY a.created_at DESC'
		);

		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function deleteApplicationById($applicationId)
	{
		$stmt = $this->conn->prepare(
			'UPDATE applications
			 SET deleted_at = NOW()
			 WHERE id = :id
			   AND deleted_at IS NULL'
		);

		$stmt->execute([':id' => (int) $applicationId]);
		return $stmt->rowCount() > 0;
	}

	public function getAllReviews()
	{
		$stmt = $this->conn->prepare(
			'SELECT r.id, r.rating, r.comments, r.created_at,
			        p.id AS project_id, p.title AS project_title,
			        u.id AS reviewer_id, u.user_name AS reviewer_name, u.email_address AS reviewer_email
			 FROM reviews r
			 INNER JOIN projects p ON p.id = r.project_id
			 INNER JOIN users u ON u.id = r.reviewer_id
			 WHERE r.deleted_at IS NULL
			 ORDER BY r.created_at DESC'
		);

		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function deleteReviewById($reviewId)
	{
		$stmt = $this->conn->prepare(
			'UPDATE reviews
			 SET deleted_at = NOW()
			 WHERE id = :id
			   AND deleted_at IS NULL'
		);

		$stmt->execute([':id' => (int) $reviewId]);
		return $stmt->rowCount() > 0;
	}
}
