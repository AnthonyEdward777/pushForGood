<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= basePath() ?>/public/images/favicon.png">
    <title>Sign Up - Push For Good</title>
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/credentials.css">
</head>

<body>
    <main class="auth-page">
        <div class="credentialsContainer">
            <img src="<?= basePath() ?>/public/images/favicon.png" alt="Logo" class="logo">
            <h2>Sign Up</h2>

            <?php if (!empty($errorMessage)): ?>
                <p class="formMessage error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form class="credentialsForm" method="POST" action="<?= basePath() ?>/register" data-enhanced-validation="true">
                <input type="text" name="name" placeholder="Full Name" minlength="2" maxlength="100" autocomplete="name" title="Name must be between 2 and 100 characters." required data-error-required="Full name is required." data-error-minlength="Name must be at least 2 characters." data-error-maxlength="Name must be 100 characters or fewer.">
                <small class="fieldHint">Enter your real name for project applications.</small>
                <input type="email" name="email" placeholder="Email" autocomplete="email" required data-error-required="Email is required." data-error-email="Please enter a valid email address.">
                <small class="fieldHint">We will use this for login and notifications.</small>
                <input type="password" name="password" placeholder="Password" minlength="8" maxlength="72" autocomplete="new-password" title="Password must be at least 8 characters." required data-error-required="Password is required." data-error-minlength="Password must be at least 8 characters." data-error-maxlength="Password must be 72 characters or fewer.">
                <small class="fieldHint">Use at least 8 characters.</small>

                <select name="entity" id="entity" required data-error-required="Please choose Student or NGO.">
                    <option value="student">Student</option>
                    <option value="ngo">NGO</option>
                </select>
                <small class="fieldHint">Choose NGO only if you represent an organization.</small>

                <div id="ngoFields" class="fieldGroup is-hidden">
                    <input type="text" id="licenseNumber" name="licenseNumber" placeholder="NGO License Number" minlength="4" maxlength="100" title="License number should be at least 4 characters." data-error-required="NGO license number is required." data-error-minlength="License number must be at least 4 characters." data-error-maxlength="License number must be 100 characters or fewer.">
                    <small class="fieldHint">Required for NGO registration.</small>
                </div>

                <button type="submit">Register</button>
            </form>

            <p class="formLinks">Already have an account? <a href="<?= basePath() ?>/login">Log in</a></p>
        </div>
    </main>
    <script src="<?= basePath() ?>/public/scripts/form_validation.js"></script>
    <script>
        const entitySelect = document.getElementById('entity');
        const ngoFields = document.getElementById('ngoFields');
        const licenseInput = document.getElementById('licenseNumber');

        function syncNgoFields() {
            const isNgo = entitySelect.value === 'ngo';
            ngoFields.classList.toggle('is-hidden', !isNgo);
            licenseInput.required = isNgo;
        }

        entitySelect.addEventListener('change', syncNgoFields);
        syncNgoFields();
    </script>
</body>

</html>