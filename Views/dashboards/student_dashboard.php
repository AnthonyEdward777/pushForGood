<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | PushForGood</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .admin-box {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-top: 6px solid #d9534f;
            /* Admin Red */
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .welcome-text h1 {
            margin: 0;
            color: #333;
        }

        .role-badge {
            background: #d9534f;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #d9534f;
        }

        .btn-logout {
            background: #333;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
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

        <p style="color: #666; margin-top: 20px;">
            This is the Master Administrative Interface. You have full oversight of all Students and NGOs.
        </p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number">1,240</div>
            </div>
            <div class="stat-card">
                <h3>Active NGOs</h3>
                <div class="number">42</div>
            </div>
            <div class="stat-card">
                <h3>Pending Approvals</h3>
                <div class="number">7</div>
            </div>
        </div>

        <div style="margin-top: 40px; padding: 20px; border: 1px dashed #ccc; border-radius: 8px; background: #fffcfc;">
            <h2 style="color: #d9534f; margin-top: 0;">System Logs</h2>
            <p>Auth Flow: <strong>STABLE</strong></p>
            <p>Database: <strong>CONNECTED</strong></p>
        </div>
    </div>

</body>

</html>