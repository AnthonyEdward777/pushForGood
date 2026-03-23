<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> | PushForGood</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            padding: 50px;
        }

        .project-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .status-bar {
            background: #333;
            color: white;
            padding: 10px 25px;
            font-size: 0.8em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            padding: 40px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
            padding: 20px;
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .meta-item label {
            display: block;
            font-size: 0.8em;
            color: #888;
            text-transform: uppercase;
        }

        .meta-item span {
            font-weight: bold;
            color: #333;
        }

        .description {
            line-height: 1.8;
            color: #555;
            font-size: 1.1em;
        }

        .action-tray {
            padding: 20px 40px;
            background: #f1f1f1;
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-edit {
            background: #3498db;
            color: white;
        }

        /* NGO Blue */
        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        /* Admin/NGO Red */
        .btn-apply {
            background: #2ecc71;
            color: white;
            width: 100%;
            text-align: center;
        }

        /* Student Green */
    </style>
</head>

<body>

    <div class="project-container">
        <div class="status-bar">
            Project ID: #<?= $project['id'] ?> • Open for Applications
        </div>

        <div class="content">
            <h1 style="margin-top: 0; color: #2c3e50;"><?= htmlspecialchars($project['title']) ?></h1>

            <div class="description">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Required Skills</label>
                    <span><?= htmlspecialchars($project['required_skills'] ?: 'General Volunteer') ?></span>
                </div>
                <div class="meta-item">
                    <label>Location</label>
                    <span><?= htmlspecialchars($project['location'] ?: 'Remote') ?></span>
                </div>
                <div class="meta-item">
                    <label>Deadline</label>
                    <span style="color: #e74c3c;"><?= date('M d, Y', strtotime($project['deadline'])) ?></span>
                </div>
                <div class="meta-item">
                    <label>Posted By</label>
                    <span>NGO #<?= $project['ngo_id'] ?></span>
                </div>
            </div>
        </div>

        <div class="action-tray">
            <?php if ($_SESSION['userId'] == $project['ngo_id']): ?>
                <a href="/pushforgood/projects/edit/<?= $project['id'] ?>" class="btn btn-edit">Edit Listing</a>
                <a href="/pushforgood/projects/delete/<?= $project['id'] ?>" class="btn btn-delete" onclick="return confirm('Archive this project?')">Delete Project</a>
            <?php elseif ($_SESSION['userRole'] == 'student'): ?>
                <a href="/pushforgood/projects/apply/<?= $project['id'] ?>" class="btn btn-apply">Apply to Volunteer</a>
            <?php else: ?>
                <a href="/pushforgood/dashboard" class="btn btn-edit">Back to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>