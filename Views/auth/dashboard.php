<html lang="en">
    <head>
        <?php
            include __DIR__ . '/../../repeated.php';
            seo();
        ?>
        <title>Dashboard - Push For Good</title>
        <link rel="stylesheet" href="public/styleSheets/credentials.css">
    </head>
    <body>
        <center>
            <div class="credentialsContainer">
                <img height="10%" src="public/images/favicon.png" alt="Logo" class="logo">
                <h2>Dashboard</h2>
                <p class="formMessage">Welcome, <?php echo htmlspecialchars($name); ?>!</p>
                <p class="formMessage">Account type: <?php echo htmlspecialchars($role); ?></p>
                <p class="formMessage"><?php echo htmlspecialchars($dashboardText); ?></p>
                <a class="actionLink" href="index.php?page=logout">Logout</a>
            </div>
        </center>
    </body>
</html>
