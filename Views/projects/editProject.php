<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Project</title>
    <?php seo(); ?>
</head>

<body>

    <div class="container">
        <h2>Edit Project</h2>

        <form method="POST" action="/pushforgood/projects/edit">
            <input type="hidden" name="id" value="<?= $data['id']; ?>">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($data['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($data['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="skills">Skills Required</label>
                <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($data['required_skills'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($data['location'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" id="deadline" name="deadline" value="<?= $data['deadline']; ?>" required>
            </div>

            <button type="submit" class="btn">Update Project</button>
            <a href="/pushforgood/dashboard" class="btn">Cancel</a>
        </form>
    </div>

</body>

</html>