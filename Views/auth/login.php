<html lang="en">
    <head>
        <?php
            include __DIR__ . '/../../repeated.php';
            seo();
        ?>
        <title>Login - Push For Good</title>
        <link rel="stylesheet" href="public/styleSheets/credentials.css">
    </head>
    <body>
        <center>
            <div class="credentialsContainer">
                <img height="10%" src="public/images/favicon.png" alt="Logo" class="logo">
                <h2>Log in</h2>

                <?php if (!empty($errorMessage)): ?>
                    <p class="formMessage error"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>

                <form class="credentialsForm" method="POST" action="index.php?page=login" novalidate>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Log in</button>
                </form>

                <p class="formLinks">No account yet? <a href="index.php?page=signup">Sign up</a></p>
            </div>
        </center>
    </body>
</html>
