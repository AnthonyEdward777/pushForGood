<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Dashboard - PushForGood</title>
    <style>
        /* Quick temporary inline styles matching the student dashboard */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            padding: 20px;
            color: #333;
        }

        .dashboard-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .project-card {
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #fafafa;
        }

        .project-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .tag {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .btn-logout {
            background: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-primary {
            background: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <div class="header">
            <div>
                <h1>Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
                <p>Account Type: <strong><?= htmlspecialchars(strtoupper($role), ENT_QUOTES, 'UTF-8') ?></strong></p>
            </div>
            <a href="/pushforgood/logout" class="btn-logout">Log Out</a>
        </div>

        <div class="action-bar">
            <h2>Your Active Listings</h2>
            <a href="/pushforgood/projects/create" class="btn-primary">+ Post New Opportunity</a>
        </div>

        <div class="project-card">
            <h3>Network Security Audit</h3>
            <div class="stats">
                <a href="/pushforgood/projects/edit?id=<?= $project['id'] ?>" style="color: #3498db; margin-right: 15px;">Edit Project</a>

                <a href="/pushforgood/projects/delete?id=<?= $project['id'] ?>"
                    style="color: #e74c3c;"
                    onclick="return confirm('Are you sure you want to delete this project?');">
                    Delete
                </a>
            </div>
        </div>

        <p>Manage your volunteer postings and review student applicants here.</p>

    </div>

</body>

</html>