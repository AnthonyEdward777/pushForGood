<?php
// Models/Student.php
require_once 'User.php';
require_once 'DashboardUI.php';

class Student extends User implements DashboardUI
{
    public function register($data)
    {
        $this->lastError = '';
        $roleId = $this->getRoleId('student');

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
                error_log('Student registration failed: ' . $exception->getMessage());
                $this->lastError = 'Registration failed. Please try again.';
            }
            return false;
        }
    }

    public function generateDashboard()
    {
        return 'dashboards/student_dashboard';
    }
}
