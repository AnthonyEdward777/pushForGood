<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // Make sure the path to repeated.php is correct based on your new folder structure!
    include __DIR__ . '/../../repeated.php';
    seo();
    ?>
    <title>Sign Up - Push For Good</title>
    <link rel="stylesheet" href="/pushforgood/public/stylesheets/credentials.css">
</head>

<body>

    <main class="credentialsContainer">
        <img height="10%" src="/pushforgood/public/images/favicon.png" alt="Logo" class="logo">
        <h2>Sign Up</h2>

        <?php if (!empty($errorMessage)): ?>
            <p class="formMessage error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form class="credentialsForm" method="POST" action="/pushforgood/register" novalidate>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="entity" id="entity" required>
                <option value="student">Student</option>
                <option value="ngo">NGO</option>
            </select>

            <div id="ngoFields" class="fieldGroup" style="display:none;">
                <input type="text" id="licenseNumber" name="licenseNumber" placeholder="NGO License Number">
            </div>

            <button type="submit">Register</button>
        </form>

        <p class="formLinks">Already have an account? <a href="/pushforgood/login">Log in</a></p>
    </main>

    <script>
        // This JavaScript is perfect. It handles the dynamic form display exactly as it should.
        const entitySelect = document.getElementById('entity');
        const ngoFields = document.getElementById('ngoFields');
        const licenseInput = document.getElementById('licenseNumber');

        function syncNgoFields() {
            const isNgo = entitySelect.value === 'ngo';
            ngoFields.style.display = isNgo ? 'block' : 'none';
            licenseInput.required = isNgo;
        }

        entitySelect.addEventListener('change', syncNgoFields);
        syncNgoFields();
    </script>
</body>

</html>