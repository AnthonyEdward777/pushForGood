<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | PushForGood</title>
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/app_theme.css">
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/student_dashboard.css">
</head>

<body>

    <div class="dashboard-box">
        <div class="header">
            <div class="welcome-text">
                <h1>Welcome, <?= htmlspecialchars($name) ?></h1>
                <span class="role-badge"><?= htmlspecialchars($role) ?> Access</span>
            </div>
            <a href="<?= basePath() ?>/logout" class="btn-logout">Logout</a>
        </div>

        <?php if (!empty($flashSuccess)): ?>
            <div class="flash flash-success"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <h2 class="section-heading">Available Projects</h2>
        <p class="section-subtitle">Browse open volunteer opportunities and apply directly.</p>

        <div class="filters-box">
            <form class="filters-form" method="GET" action="<?= basePath() ?>/dashboard" data-enhanced-validation="true">
                <div>
                    <label for="type">Project Type</label>
                    <select id="type" name="type">
                        <option value="">All Types</option>
                        <?php foreach (($projectTypes ?? []) as $type): ?>
                            <option value="<?= (int) $type['id'] ?>" <?= ((string) ($projectFilters['type'] ?? '') === (string) $type['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="duration">Duration</label>
                    <select id="duration" name="duration">
                        <option value="">Any Duration</option>
                        <option value="short" <?= (($projectFilters['duration'] ?? '') === 'short') ? 'selected' : '' ?>>Short (0-30 days)</option>
                        <option value="medium" <?= (($projectFilters['duration'] ?? '') === 'medium') ? 'selected' : '' ?>>Medium (31-90 days)</option>
                        <option value="long" <?= (($projectFilters['duration'] ?? '') === 'long') ? 'selected' : '' ?>>Long (90+ days)</option>
                    </select>
                </div>

                <div>
                    <label for="skill">Skill Required</label>
                    <input id="skill" type="text" name="skill" maxlength="100" value="<?= htmlspecialchars($projectFilters['skill'] ?? '') ?>" placeholder="e.g. PHP, Design, Data Entry" title="Max 100 characters." data-error-maxlength="Skill keyword must be 100 characters or fewer.">
                    <small class="field-hint">Tip: try one keyword at a time for better results.</small>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <a href="<?= basePath() ?>/dashboard" class="btn btn-clear">Reset</a>
                </div>
            </form>
        </div>

        <div class="project-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <?php $alreadyApplied = in_array((int) $project['id'], $appliedProjectIds ?? [], true); ?>
                    <div class="project-card">
                        <h3><?= htmlspecialchars($project['title']) ?></h3>
                        <div class="project-meta">
                            NGO: <?= htmlspecialchars($project['ngo_name'] ?? 'Unknown NGO') ?> |
                            Type: <?= htmlspecialchars($project['category_name'] ?? 'Uncategorized') ?> |
                            Deadline: <?= htmlspecialchars($project['deadline'] ?? 'N/A') ?> |
                            Status: <?= htmlspecialchars($project['status'] ?? 'N/A') ?>
                        </div>
                        <div class="project-description">
                            <?= nl2br(htmlspecialchars($project['description'] ?? '')) ?>
                        </div>
                        <div class="project-actions">
                            <a href="<?= basePath() ?>/projects/view?id=<?= (int) $project['id'] ?>" class="btn btn-view">View Details</a>

                            <?php if (strtolower($project['status'] ?? '') !== 'open'): ?>
                                <button type="button" class="btn btn-applied" disabled>Closed</button>
                            <?php elseif ($alreadyApplied): ?>
                                <button type="button" class="btn btn-applied" disabled>Applied</button>
                            <?php else: ?>
                                <a href="<?= basePath() ?>/projects/view?id=<?= (int) $project['id'] ?>" class="btn btn-apply">Apply</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="project-card">
                    <p class="zero-margin">No projects available right now. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="applications-section">
            <h2 class="section-title">My Applications</h2>
            <p class="muted">Track your submissions and current application status.</p>

            <?php if (!empty($studentApplications)): ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>NGO</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Contract</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentApplications as $application): ?>
                            <?php $status = strtolower($application['status'] ?? 'pending'); ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($application['project_title'] ?? 'Untitled Project') ?></strong><br>
                                    <span class="muted">Deadline: <?= htmlspecialchars($application['project_deadline'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= htmlspecialchars($application['ngo_name'] ?? 'Unknown NGO') ?></td>
                                <td><?= !empty($application['applied_at']) ? date('M d, Y', strtotime($application['applied_at'])) : 'N/A' ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($status) ?>">
                                        <?= htmlspecialchars($application['status'] ?? 'Pending') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($status === 'accepted' && !empty($application['contract_id'])): ?>
                                        <a href="<?= basePath() ?>/contracts/download?application_id=<?= (int) $application['id'] ?>" class="btn btn-view">Download PDF</a>
                                    <?php elseif ($status === 'accepted'): ?>
                                        <span class="muted">Pending</span>
                                    <?php else: ?>
                                        <span class="muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= basePath() ?>/projects/view?id=<?= (int) $application['project_id'] ?>" class="btn btn-view">Open</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="project-card">
                    <p class="zero-margin">You have not applied to any projects yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= basePath() ?>/public/scripts/form_validation.js"></script>

</body>

</html>