<?php
class User
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

    public function findRoleIdForEntity($entity)
    {
        $entity = strtolower(trim($entity));

        if ($entity === 'user') {
            $query = "SELECT id FROM roles WHERE LOWER(role_name) IN ('student', 'user') ORDER BY CASE WHEN LOWER(role_name) = 'student' THEN 0 ELSE 1 END LIMIT 1";
            $stmt = $this->conn->query($query);
        } else {
            $stmt = $this->conn->prepare('SELECT id FROM roles WHERE LOWER(role_name) = :role LIMIT 1');
            $stmt->execute([':role' => $entity]);
        }

        $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $roleData ? (int) $roleData['id'] : null;
    }

    public function register($name, $email, $password, $entity, $licenseNumber = null)
    {
        $this->lastError = '';
        $roleId = $this->findRoleIdForEntity($entity);
        if ($roleId === null) {
            $this->lastError = 'Selected role was not found in database.';
            return false;
        }

        $this->conn->beginTransaction();

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertUser = $this->conn->prepare(
                'INSERT INTO users (role_id, name, email, password)
                 VALUES (:roleId, :name, :email, :password)'
            );
            $insertUser->execute([
                ':roleId' => $roleId,
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);

            $newUserId = (int) $this->conn->lastInsertId();

            if (strtolower($entity) === 'ngo') {
                if (empty($licenseNumber)) {
                    throw new RuntimeException('License number required for NGO registration.');
                }

                $insertNgo = $this->conn->prepare(
                    'INSERT INTO ngo_profiles (user_id, license_number, mission_statement)
                     VALUES (:userId, :licenseNumber, :missionStatement)'
                );
                $insertNgo->execute([
                    ':userId' => $newUserId,
                    ':licenseNumber' => $licenseNumber,
                    ':missionStatement' => null,
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            if ($exception instanceof PDOException && (string) $exception->getCode() === '23000') {
                $this->lastError = 'That email is already registered.';
            } else {
                $this->lastError = $exception->getMessage() ?: 'Registration failed. Please try again.';
            }

            return false;
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare(
            'SELECT u.id, u.name, u.password, r.role_name
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email
             LIMIT 1'
        );
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            return [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'role' => $row['role_name'],
            ];
        }

        return false;
    }
}
