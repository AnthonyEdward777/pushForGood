<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

<<<<<<< HEAD
require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood/models/Project.php';
=======
require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood/models/project.php';
>>>>>>> a5ee4160f121f39ef1b7a1493597feea11bdbe3d
require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood/repeated.php';

// Instantiate and fetch projects
$project = new Project();
$projects = $project->getProjectsByNgo($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>NGO Dashboard</title>
    <?php seo(); ?>
</head>
<body>

<div class="container">
    <h2>My Projects</h2>
    <a href="createProject.php" class="btn">Create New Project</a>

    <?php if ($projects && $projects->rowCount() > 0): ?>
        <?php while($row = $projects->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="project-card">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($row['deadline']); ?></p>

                <a href="editProject.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                
                <a href="/pushForGood/Controllers/NGO/projectController.php?action=delete&id=<?php echo $row['id']; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
            </div>
        <?php } ?>
    <?php else: ?>
        <p>No projects found. Create your first project!</p>
    <?php endif; ?>
</div>

</body>
</html>