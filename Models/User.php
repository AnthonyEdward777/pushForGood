<?php
// Models/User.php

abstract class User
{
    protected $conn;
    protected $lastError = '';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    // Abstract method: Forces children to implement their own registration flow
    abstract public function register($data);

    // Shared Login Logic
    public static function login($db, $email, $password)
    {
        $stmt = $db->prepare(
            'SELECT u.id, u.user_name, u.user_password, t.type_name as role_name
            FROM users u
            INNER JOIN user_types t ON t.id = u.user_type_id
            WHERE u.email_address = :email_address AND u.deleted_at IS NULL
            LIMIT 1'
        );
        $stmt->execute([':email_address' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['user_password'])) {
            return [
                'id' => (int) $row['id'],
                'name' => $row['user_name'],
                'role' => $row['role_name'],
            ];
        }

        return false;
    }

    // Helper function used by children during registration
    protected function getRoleId($entityName)
    {
        $stmt = $this->conn->prepare('SELECT id FROM user_types WHERE LOWER(type_name) = :role LIMIT 1');
        $stmt->execute([':role' => strtolower($entityName)]);
        $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $roleData ? (int) $roleData['id'] : null;
    }
}
