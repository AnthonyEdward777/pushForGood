<html lang="en">
    <head>
        <?php
            include __DIR__ . '/../../repeated.php';
            seo();
        ?>
        <title>Sign Up - Push For Good</title>
        <link rel="stylesheet" href="public/styleSheets/credentials.css">
    </head>
    <body>
        <center>
            <div class="credentialsContainer">
                <img height="10%" src="public/images/favicon.png" alt="Logo" class="logo">
                <h2>Sign Up</h2>

                <?php if (!empty($errorMessage)): ?>
                    <p class="formMessage error"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php endif; ?>

                <form class="credentialsForm" method="POST" action="index.php?page=signup" novalidate>
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <select name="entity" id="entity" required>
                        <option value="user">User</option>
                        <option value="ngo">NGO</option>
                        <option value="admin">Admin</option>
                    </select>

                    <div id="ngoFields" class="fieldGroup" style="display:none;">
                        <input type="text" id="licenseNumber" name="licenseNumber" placeholder="NGO License Number">
                    </div>

                    <button type="submit">Register</button>
                </form>

                <p class="formLinks">Already have an account? <a href="index.php?page=login">Log in</a></p>
            </div>
        </center>

        <script>
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
