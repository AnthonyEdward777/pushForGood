<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project | PushForGood</title>
    <?php if (function_exists('seo')) seo(); ?>
    <style>
        /* Basic styling to match your dashboard vibe */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-cancel {
            background: #95a5a6;
            margin-left: 10px;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Post a New Opportunity</h2>

        <form method="POST" action="/pushforgood/projects/create">

            <div class="form-group">
                <label for="title">Project Title</label>
                <input type="text" id="title" name="title" placeholder="e.g., Website Redesign for Charity" required>
            </div>

            <div class="form-group">
                <label for="description">Detailed Description</label>
                <textarea id="description" name="description" placeholder="Describe the mission and what the volunteer will do..." required></textarea>
            </div>

            <div class="form-group">
                <label for="skills">Skills Required</label>
                <input type="text" id="skills" name="skills" placeholder="e.g., PHP, Graphic Design, Marketing">
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Cairo, Egypt or Remote">
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" id="deadline" name="deadline" required>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn">Launch Project</button>
                <a href="/pushforgood/dashboard" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>

</html>