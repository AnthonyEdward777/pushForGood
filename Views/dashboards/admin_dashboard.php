<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PushForGood</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/app_theme.css">
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/admin_dashboard.css">
</head>

<body>

    <div class="admin-box">
        <div class="header">
            <div class="welcome-text">
                <h1>Welcome, <?= htmlspecialchars($name) ?></h1>
                <span class="role-badge"><?= htmlspecialchars($role) ?> Access</span>
            </div>
            <a href="/pushforgood/logout" class="btn-logout">Logout</a>
        </div>

        <?php if (!empty($flashSuccess)): ?>
            <div class="flash flash-success"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>Manage Students and NGOs</h2>
            <p>Remove student and NGO accounts from the platform.</p>

            <div class="table-wrap">
                <?php if (!empty($users)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($user['user_name']) ?></strong><br>
                                        <span class="muted">ID: <?= (int) $user['id'] ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($user['role_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email_address']) ?></td>
                                    <td><?= !empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?></td>
                                    <td>
                                        <form method="POST" action="/pushforgood/admin/users/delete" onsubmit="return confirm('Remove this user account? This may also remove related records.');">
                                            <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                            <button type="submit" class="btn btn-delete">Remove User</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="muted">No student or NGO users found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2>Manage Applications</h2>
            <p>Remove any application record from the system.</p>

            <div class="table-wrap">
                <?php if (!empty($applications)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Application</th>
                                <th>Student</th>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <?php $status = strtolower($application['status'] ?? 'pending'); ?>
                                <tr>
                                    <td>
                                        <strong>#<?= (int) $application['id'] ?></strong><br>
                                        <span class="muted"><?= !empty($application['applied_at']) ? date('M d, Y', strtotime($application['applied_at'])) : 'N/A' ?></span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($application['student_name']) ?></strong><br>
                                        <span class="muted"><?= htmlspecialchars($application['student_email']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($application['project_title']) ?></strong><br>
                                        <span class="muted">Project ID: <?= (int) $application['project_id'] ?></span>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?= htmlspecialchars($status) ?>">
                                            <?= htmlspecialchars($application['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="/pushforgood/admin/applications/delete" onsubmit="return confirm('Remove this application?');">
                                            <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
                                            <button type="submit" class="btn btn-delete">Remove Application</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="muted">No applications found.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2>Manage Reviews</h2>
            <p>Remove any review record from the system.</p>

            <div class="table-wrap">
                <?php if (!empty($reviews)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Review</th>
                                <th>Reviewer</th>
                                <th>Project</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= (int) $review['id'] ?></strong><br>
                                        <span class="muted"><?= htmlspecialchars($review['comments'] ?? '') ?></span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($review['reviewer_name']) ?></strong><br>
                                        <span class="muted"><?= htmlspecialchars($review['reviewer_email']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($review['project_title']) ?></strong><br>
                                        <span class="muted">Project ID: <?= (int) $review['project_id'] ?></span>
                                    </td>
                                    <td><?= (int) $review['rating'] ?>/5</td>
                                    <td>
                                        <form method="POST" action="/pushforgood/admin/reviews/delete" onsubmit="return confirm('Remove this review?');">
                                            <input type="hidden" name="review_id" value="<?= (int) $review['id'] ?>">
                                            <button type="submit" class="btn btn-delete">Remove Review</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="muted">No reviews found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>