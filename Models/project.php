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
        // We use a subquery to translate the user_id (5) into the ngo_id (3)
        $query = "INSERT INTO projects 
    (ngo_id, category_id, title, description, skills, location, deadline, status) 
    VALUES (
        (SELECT id FROM ngos WHERE user_id = ? LIMIT 1), 
        1, ?, ?, ?, ?, ?, 'Open'
    )";

        $stmt = $this->db->prepare($query);
        // Notice we are still passing the $user_id from the session as the first parameter
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
        return $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch(), not fetchAll(), because we only want one row
    }

    // Update the project securely
    public function updateProject($project_id, $user_id, $title, $description, $skills, $location, $deadline)
    {
        $query = "UPDATE projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  SET p.title = ?, p.description = ?, p.skills = ?, p.location = ?, p.deadline = ?
                  WHERE p.id = ? AND n.user_id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$title, $description, $skills, $location, $deadline, $project_id, $user_id]);
    }

    // Delete the project securely
    public function deleteProject($project_id, $user_id)
    {
        $query = "DELETE p FROM projects p
                  JOIN ngos n ON p.ngo_id = n.id
                  WHERE p.id = ? AND n.user_id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$project_id, $user_id]);
    }
}
?>