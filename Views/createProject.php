<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood-main/repeated.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Project</title>
    <?php seo(); ?>
</head>
<body>

<div class="container">
    <h2>Create New Project</h2>

    
    <form method="POST" action="/pushForGood-main/Controllers/NGO/projectController.php?action=create">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" placeholder="Project Title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Project Description" required></textarea>
        </div>

        <div class="form-group">
            <label for="skills">Skills</label>
            <input type="text" id="skills" name="skills" placeholder="Required Skills (comma separated)">
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" placeholder="Location">
        </div>

        <div class="form-group">
            <label for="deadline">Application Deadline</label>
            <input type="date" id="deadline" name="deadline" required>
        </div>

        <button type="submit" class="btn">Create Project</button>
        <a href="dashboard.php" class="btn">Cancel</a>
    </form>
</div>

</body>
</html>