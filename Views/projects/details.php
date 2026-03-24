<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> | PushForGood</title>
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/app_theme.css">
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/project_details.css">
</head>

<body>

    <div class="project-container">
        <a href="<?= basePath() ?>/dashboard" class="back-arrow" aria-label="Back to dashboard">&larr;</a>
        <div class="status-bar">
            Project ID: #<?= (int) $project['id'] ?> • <?= htmlspecialchars($project['status'] ?? 'Open') ?>
        </div>

        <?php if (!empty($flashSuccess)): ?>
            <div class="flash flash-success"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="content">
            <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>

            <div class="description">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Required Skills</label>
                    <span><?= htmlspecialchars($project['skills'] ?: 'General Volunteer') ?></span>
                </div>
                <div class="meta-item">
                    <label>Location</label>
                    <span><?= htmlspecialchars($project['location'] ?: 'Remote') ?></span>
                </div>
                <div class="meta-item">
                    <label>Deadline</label>
                    <span class="deadline-text"><?= !empty($project['deadline']) ? date('M d, Y', strtotime($project['deadline'])) : 'N/A' ?></span>
                </div>
                <div class="meta-item">
                    <label>Posted By</label>
                    <span><?= htmlspecialchars($project['ngo_name'] ?? ('NGO #' . (int) ($project['ngo_id'] ?? 0))) ?></span>
                </div>
            </div>
        </div>

        <div class="action-tray">
            <?php if (strtolower($_SESSION['userRole'] ?? '') === 'ngo'): ?>
                <a href="<?= basePath() ?>/projects/edit?id=<?= (int) $project['id'] ?>" class="btn btn-edit">Edit Listing</a>
                <a href="<?= basePath() ?>/projects/delete?id=<?= (int) $project['id'] ?>" class="btn btn-delete" onclick="return confirm('Archive this project?')">Delete Project</a>
            <?php elseif (strtolower($_SESSION['userRole'] ?? '') === 'student'): ?>
                <?php if (!empty($alreadyApplied)): ?>
                    <button type="button" class="btn btn-secondary" disabled>You already applied</button>
                <?php elseif (strtolower($project['status'] ?? '') !== 'open'): ?>
                    <button type="button" class="btn btn-secondary" disabled>Project is closed</button>
                <?php else: ?>
                    <form method="POST" action="<?= basePath() ?>/projects/apply" class="apply-form" enctype="multipart/form-data" data-enhanced-validation="true">
                        <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                        <textarea name="comment" rows="4" maxlength="1000" placeholder="Tell the NGO why you are a good fit (optional)" title="Max 1000 characters." data-error-maxlength="Comment must be 1000 characters or fewer."></textarea>
                        <small class="field-hint">Optional, but a short intro increases your chance of acceptance.</small>
                        <input type="file" name="submission_file" accept=".pdf,.jpg,.jpeg,application/pdf,image/jpeg" required data-error-required="Please upload your CV.">
                        <small class="field-hint">Allowed formats: PDF or JPG/JPEG.</small>
                        <button type="submit" class="btn btn-apply">Apply to Volunteer</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= basePath() ?>/dashboard" class="btn btn-edit">Back to Dashboard</a>
            <?php endif; ?>
        </div>

        <?php if (strtolower($_SESSION['userRole'] ?? '') === 'ngo'): ?>
            <div class="applications-box">
                <h2>Student Applications</h2>
                <?php if (!empty($applications)): ?>
                    <table class="applications-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Comment</th>
                                <th>CV</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <?php $status = strtolower($application['status'] ?? 'pending'); ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($application['student_name'] ?? 'Unknown') ?></strong><br>
                                        <small><?= htmlspecialchars($application['student_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($application['comment'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($application['file_path'])): ?>
                                            <a href="<?= basePath() ?>/public/<?= htmlspecialchars($application['file_path']) ?>" target="_blank" rel="noopener">View CV</a>
                                        <?php else: ?>
                                            <span>No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($application['status'] ?? 'Pending') ?></span>
                                    </td>
                                    <td>
                                        <?php if ($status === 'pending'): ?>
                                            <form class="inline-form" method="POST" action="<?= basePath() ?>/applications/update-status">
                                                <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
                                                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                                                <input type="hidden" name="status" value="Accepted">
                                                <button type="submit" class="btn btn-apply">Approve</button>
                                            </form>
                                            <form class="inline-form" method="POST" action="<?= basePath() ?>/applications/update-status\">
                                                <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
                                                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" class="btn btn-delete">Reject</button>
                                            </form>
                                        <?php elseif ($status === 'accepted' && !empty($application['contract_id'])): ?>
                                            <a href="<?= basePath() ?>/contracts/download?application_id=<?= (int) $application['id'] ?>" class="btn btn-edit">Download Contract</a>
                                        <?php elseif ($status === 'accepted'): ?>
                                            <span>Contract pending</span>
                                        <?php else: ?>
                                            <span>-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No applications submitted yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (strtolower($_SESSION['userRole'] ?? '') === 'student'): ?>
            <div class="reviews-box">
                <h2>Leave a Review</h2>
                <?php if (!empty($alreadyReviewed)): ?>
                    <p class="muted-note">You already reviewed this project.</p>
                <?php elseif (!empty($canLeaveReview)): ?>
                    <form class="review-form" method="POST" action="<?= basePath() ?>/reviews/create" data-enhanced-validation="true">
                        <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required data-error-required="Please select a rating.">
                            <option value="">Choose rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>

                        <label for="comments">Comments</label>
                        <textarea id="comments" name="comments" rows="4" maxlength="1000" placeholder="Share your project experience" title="Max 1000 characters." data-error-maxlength="Comments must be 1000 characters or fewer."></textarea>
                        <small class="field-hint">Keep it constructive and specific.</small>

                        <button type="submit" class="btn btn-apply">Submit Review</button>
                    </form>
                <?php else: ?>
                    <p class="muted-note">You can review this project after your application is accepted.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="reviews-box">
            <h2>Project Reviews</h2>
            <?php if (!empty($projectReviews)): ?>
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projectReviews as $review): ?>
                            <tr>
                                <td><?= htmlspecialchars($review['reviewer_name'] ?? 'Unknown') ?></td>
                                <td><?= (int) $review['rating'] ?>/5</td>
                                <td><?= nl2br(htmlspecialchars($review['comments'] ?? '')) ?></td>
                                <td><?= !empty($review['created_at']) ? date('M d, Y', strtotime($review['created_at'])) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="muted-note">No reviews yet for this project.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= basePath() ?>/public/scripts/form_validation.js"></script>

</body>

</html>