<?php

class Project
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createProject($user_id, $title, $description, $skills, $location, $deadline)
    {
        $query = "INSERT INTO projects 
    (ngo_id, category_id, title, description, skills, location, deadline, status) 
    VALUES (
        (SELECT id FROM ngos WHERE user_id = ? LIMIT 1), 
        1, ?, ?, ?, ?, ?, 'Open'
    )";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$user_id, $title, $description, $skills, $location, $deadline]);
    }

    public function getProjectsByUserId($user_id)
    {
        $query = "SELECT p.* FROM projects p 
                  JOIN ngos n ON p.ngo_id = n.id 
                  WHERE n.user_id = ? 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectByIdAndUser($project_id, $user_id)
    {
        $query = "SELECT p.* FROM projects p 
                  JOIN ngos n ON p.ngo_id = n.id 
                  WHERE p.id = ? AND n.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$project_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProject($project_id, $user_id, $title, $description, $skills, $location, $deadline)
    {
        $query = "UPDATE projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  SET p.title = ?, p.description = ?, p.skills = ?, p.location = ?, p.deadline = ?
                  WHERE p.id = ? AND n.user_id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $description, $skills, $location, $deadline, $project_id, $user_id]);
    }

    public function deleteProject($project_id, $user_id)
    {
        $query = "DELETE p FROM projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  WHERE p.id = ? AND n.user_id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$project_id, $user_id]);
    }

    public function getAllProjects()
    {
        $query = "SELECT p.*, n.name AS ngo_name FROM projects p 
                  JOIN ngos n ON p.ngo_id = n.id 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAllProjects()
    {
        $query = "SELECT p.*, u.user_name AS ngo_name
                  FROM projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  JOIN users u ON n.user_id = u.id
                  WHERE p.deleted_at IS NULL
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectTypes()
    {
        $query = "SELECT c.id, c.name
                  FROM categories c
                  ORDER BY c.name ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProjects($filters = [])
    {
        $query = "SELECT p.*, u.user_name AS ngo_name, c.name AS category_name
                  FROM projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  JOIN users u ON n.user_id = u.id
                  LEFT JOIN categories c ON c.id = p.category_id
                  WHERE p.deleted_at IS NULL";

        $params = [];

        $typeId = isset($filters['type']) ? (int) $filters['type'] : 0;
        if ($typeId > 0) {
            $query .= " AND p.category_id = :type_id";
            $params[':type_id'] = $typeId;
        }

        $skill = trim($filters['skill'] ?? '');
        if ($skill !== '') {
            $query .= " AND p.skills LIKE :skill";
            $params[':skill'] = '%' . $skill . '%';
        }

        $duration = strtolower(trim($filters['duration'] ?? ''));
        if ($duration === 'short') {
            $query .= " AND p.deadline IS NOT NULL AND DATEDIFF(p.deadline, CURDATE()) BETWEEN 0 AND 30";
        } elseif ($duration === 'medium') {
            $query .= " AND p.deadline IS NOT NULL AND DATEDIFF(p.deadline, CURDATE()) BETWEEN 31 AND 90";
        } elseif ($duration === 'long') {
            $query .= " AND p.deadline IS NOT NULL AND DATEDIFF(p.deadline, CURDATE()) > 90";
        }

        $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectById($project_id)
    {
        $query = "SELECT p.*, u.user_name AS ngo_name
                  FROM projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  JOIN users u ON n.user_id = u.id
                  WHERE p.id = ? AND p.deleted_at IS NULL
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$project_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>