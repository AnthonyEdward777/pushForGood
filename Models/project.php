<?php

class Project
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createProject($ngo_id, $title, $description, $skills, $location, $deadline) {
        $query = "INSERT INTO projects 
        (ngo_id, category_id, title, description, skills, location, deadline, status) 
        VALUES (?, 1, ?, ?, ?, ?, ?, 'Open')";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$ngo_id, $title, $description, $skills, $location, $deadline]);
    }

    public function getProjectsByNgo($ngo_id) {
        $query = "SELECT * FROM projects WHERE ngo_id = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$ngo_id]);
        return $stmt;
    }

    public function getProjectById($id) {
        $query = "SELECT * FROM projects WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProject($id, $title, $description, $skills, $location, $deadline) {
        $query = "UPDATE projects 
        SET title=?, description=?, skills=?, location=?, deadline=? 
        WHERE id=? AND deleted_at IS NULL";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $description, $skills, $location, $deadline, $id]);
    }

    public function deleteProject($id) {
        $query = "UPDATE projects SET deleted_at = CURRENT_TIMESTAMP WHERE id=? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>