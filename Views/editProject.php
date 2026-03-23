<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood/models/project.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/pushForGood/repeated.php';

$project = new Project();

// Validate ID exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$data = $project->getProjectById($_GET['id']);

// Check if project exists and belongs to this NGO
if (!$data || $data['ngo_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Project</title>
    <?php seo(); ?>
</head>
<body>

<div class="container">
    <h2>Edit Project</h2>

    
    <form method="POST" action="/pushForGood/Controllers/NGO/projectController.php?action=update">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="skills">Skills</label>
            <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars($data['skills']); ?>">
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($data['location']); ?>">
        </div>

        <div class="form-group">
            <label for="deadline">Application Deadline</label>
            <input type="date" id="deadline" name="deadline" value="<?php echo $data['deadline']; ?>" required>
        </div>

        <button type="submit" class="btn">Update Project</button>
        <a href="dashboard.php" class="btn">Cancel</a>
    </form>
</div>

</body>
</html>