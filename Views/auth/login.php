<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= basePath() ?>/public/images/favicon.png">
    <title>Log In - Push For Good</title>
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/credentials.css">
</head>

<body>
    <main class="auth-page">
        <div class="credentialsContainer">
            <img src="<?= basePath() ?>/public/images/favicon.png" alt="Logo" class="logo">
            <h2>Log in</h2>

            <?php if (!empty($errorMessage)): ?>
                <p class="formMessage error"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>

            <form class="credentialsForm" method="POST" action="<?= basePath() ?>/login" data-enhanced-validation="true">
                <input type="email" name="email" placeholder="Email" autocomplete="email" required data-error-required="Email is required." data-error-email="Please enter a valid email address.">
                <small class="fieldHint">Use the same email you registered with.</small>
                <input type="password" name="password" placeholder="Password" minlength="8" autocomplete="current-password" title="Password must be at least 8 characters." required data-error-required="Password is required." data-error-minlength="Password must be at least 8 characters.">
                <small class="fieldHint">Password must be at least 8 characters.</small>
                <button type="submit">Log in</button>
            </form>

            <p class="formLinks">No account yet? <a href="<?= basePath() ?>/register">Sign up</a></p>
        </div>
    </main>
    <script src="<?= basePath() ?>/public/scripts/form_validation.js"></script>
</body>

</html>