<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood-main/config/database.php';

class Project {
    private $conn;
    private $table = "projects";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createProject($ngo_id, $title, $description, $skills, $location, $deadline) {
        $query = "INSERT INTO " . $this->table . " 
        (ngo_id, category_id, title, description, skills, location, deadline, status) 
        VALUES (?, 1, ?, ?, ?, ?, ?, 'Open')";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$ngo_id, $title, $description, $skills, $location, $deadline]);
    }

    public function getProjectsByNgo($ngo_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE ngo_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$ngo_id]);
        return $stmt;
    }

    public function getProjectById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProject($id, $title, $description, $skills, $location, $deadline) {
        $query = "UPDATE " . $this->table . " 
        SET title=?, description=?, skills=?, location=?, deadline=? 
        WHERE id=?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $description, $skills, $location, $deadline, $id]);
    }

    public function deleteProject($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>