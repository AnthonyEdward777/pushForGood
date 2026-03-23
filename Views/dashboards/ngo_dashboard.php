<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Dashboard - PushForGood</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/app_theme.css">
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/ngo_dashboard.css">
</head>

<body>

    <div class="page-shell dashboard-container">
        <div class="header">
            <div>
                <h1 class="page-title">Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
                <span class="badge"><?= htmlspecialchars(strtoupper($role), ENT_QUOTES, 'UTF-8') ?> Access</span>
            </div>
            <a href="/pushforgood/logout" class="btn btn-danger">Log Out</a>
        </div>

        <div class="action-bar">
            <h2 class="section-title">Your Active Listings</h2>
            <a href="/pushforgood/projects/create" class="btn btn-primary">+ Post New Opportunity</a>
        </div>

        <div class="project-list">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">

                        <h3><?= htmlspecialchars($project['title']) ?></h3>

                        <p class="meta">
                            <strong>Deadline:</strong> <?= htmlspecialchars($project['deadline']) ?> |
                            <strong>Status:</strong> <?= htmlspecialchars($project['status']) ?>
                        </p>

                        <div class="project-actions">
                            <a href="/pushforgood/projects/view?id=<?= $project['id'] ?>" class="btn btn-secondary">View</a>
                            <a href="/pushforgood/projects/edit?id=<?= $project['id'] ?>" class="btn btn-primary">Edit</a>
                            <a href="/pushforgood/projects/delete?id=<?= $project['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this project?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <p>You haven't posted any projects yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <p class="muted">Manage your volunteer postings and review student applicants here.</p>

    </div>

</body>

</html>