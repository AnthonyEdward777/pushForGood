<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/pushforgood/public/images/favicon.png">
    <title>Create Project | PushForGood</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/app_theme.css">
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/project_create.css">
</head>

<body>

    <div class="container">
        <h2>Post a New Opportunity</h2>

        <form method="POST" action="/pushforgood/projects/create" data-enhanced-validation="true">

            <div class="form-group">
                <label for="title">Project Title</label>
                <input type="text" id="title" name="title" placeholder="e.g., Website Redesign for Charity" minlength="5" maxlength="200" title="Title must be between 5 and 200 characters." required data-error-required="Project title is required." data-error-minlength="Title must be at least 5 characters." data-error-maxlength="Title must be 200 characters or fewer.">
                <small class="field-hint">Keep it clear and specific so students understand the mission.</small>
            </div>

            <div class="form-group">
                <label for="description">Detailed Description</label>
                <textarea id="description" name="description" minlength="20" maxlength="4000" placeholder="Describe the mission and what the volunteer will do..." required data-error-required="Description is required." data-error-minlength="Description must be at least 20 characters." data-error-maxlength="Description must be 4000 characters or fewer."></textarea>
                <small class="field-hint">Include goals, expected tasks, and impact.</small>
            </div>

            <div class="form-group">
                <label for="skills">Skills Required</label>
                <input type="text" id="skills" name="skills" maxlength="500" placeholder="e.g., PHP, Graphic Design, Marketing" title="Max 500 characters." data-error-maxlength="Skills must be 500 characters or fewer.">
                <small class="field-hint">Separate multiple skills with commas.</small>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" maxlength="255" placeholder="e.g., Cairo, Egypt or Remote" title="Max 255 characters." data-error-maxlength="Location must be 255 characters or fewer.">
                <small class="field-hint">Use "Remote" if no physical attendance is needed.</small>
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" id="deadline" name="deadline" required data-error-required="Application deadline is required.">
            </div>

            <div class="actions">
                <button type="submit" class="btn">Launch Project</button>
                <a href="/pushforgood/dashboard" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <script src="/pushforgood/public/scripts/form_validation.js"></script>

</body>

</html>