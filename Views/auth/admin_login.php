<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/pushforgood/public/images/favicon.png">
    <title>Admin Portal - Push For Good</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/credentials.css">
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/admin_login.css">
</head>

<body>
    <main class="auth-page">
        <div class="credentialsContainer">
            <img src="/pushforgood/public/images/favicon.png" alt="Logo" class="logo">
            <h2>Admin Portal</h2>
            <p class="restricted-label">Restricted Access</p>

            <?php if (!empty($errorMessage)): ?>
                <p class="formMessage error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="credentialsForm" method="POST" action="/pushforgood/admin/login" data-enhanced-validation="true">
                <input type="email" name="email" placeholder="Admin Email" autocomplete="email" required data-error-required="Admin email is required." data-error-email="Please enter a valid admin email address.">
                <small class="fieldHint">Use your admin account email.</small>
                <input type="password" name="password" placeholder="Password" minlength="8" autocomplete="current-password" title="Password must be at least 8 characters." required data-error-required="Password is required." data-error-minlength="Password must be at least 8 characters.">
                <small class="fieldHint">Minimum 8 characters.</small>
                <button type="submit" class="admin-submit">Secure Login</button>
            </form>

            <p class="formLinks"><a href="/pushforgood/login">Return to Public Login</a></p>
        </div>
    </main>
    <script src="/pushforgood/public/scripts/form_validation.js"></script>
</body>

</html>