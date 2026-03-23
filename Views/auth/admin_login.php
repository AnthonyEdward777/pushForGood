<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include __DIR__ . '/../../repeated.php';
    seo();
    ?>
    <title>Admin Portal - Push For Good</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/credentials.css">
    <style>
        /* A subtle visual cue that this is a restricted area */
        .credentialsContainer {
            border-top: 5px solid #e74c3c;
        }

        h2 {
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <main class="credentialsContainer">
        <img height="10%" src="/pushforgood/public/images/favicon.png" alt="Logo" class="logo">
        <h2>Admin Portal</h2>
        <p style="text-align: center; font-size: 0.9em; color: #666; margin-bottom: 20px;">Restricted Access</p>

        <?php if (!empty($errorMessage)): ?>
            <p class="formMessage error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form class="credentialsForm" method="POST" action="/pushforgood/admin/login" novalidate>
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" style="background-color: #e74c3c;">Secure Login</button>
        </form>

        <p class="formLinks"><a href="/pushforgood/login">Return to Public Login</a></p>
    </main>
</body>

</html>