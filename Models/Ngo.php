<?php
require_once 'User.php';
require_once 'DashboardUI.php';

class NGO extends User implements DashboardUI
{
    public function register($data)
    {
        $this->lastError = '';
        $roleId = $this->getRoleId('ngo');

        if (empty($data['licenseNumber'])) {
            $this->lastError = 'License number is required for NGOs.';
            return false;
        }

        $this->conn->beginTransaction();

        try {
            $hashedPassword = password_hash($data['user_password'], PASSWORD_DEFAULT);

            $insertUser = $this->conn->prepare(
                'INSERT INTO users (user_type_id, user_name, email_address, user_password)
                VALUES (:roleId, :user_name, :email_address, :user_password)'
            );
            $insertUser->execute([
                ':roleId' => $roleId,
                ':user_name' => $data['user_name'],
                ':email_address' => $data['email_address'],
                ':user_password' => $hashedPassword,
            ]);

            $newUserId = (int) $this->conn->lastInsertId();

            $insertNgo = $this->conn->prepare(
                'INSERT INTO ngos (user_id, license_number)
                VALUES (:userId, :licenseNumber)'
            );
            $insertNgo->execute([
                ':userId' => $newUserId,
                ':licenseNumber' => $data['licenseNumber']
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $exception) {
            $this->conn->rollBack();
            if ($exception->getCode() === '23000') {
                $this->lastError = 'That email is already registered.';
            } else {
                $this->lastError = 'Registration failed.';
            }
            return false;
        }
    }

    public function generateDashboard()
    {
        return 'dashboards/ngo_dashboard';
    }
}
