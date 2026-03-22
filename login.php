<html lang="en">
    <head>
        <?php
            include 'repeated.php';
            SEO();
        ?>
        <title>Login - Push For Good</title>
        <link rel="stylesheet" href="styleSheets/credentials.css">
    </head>
    <body>
        <?php
            session_start();
            include 'db.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $email = $_POST['email'];
                $password = $_POST['password'];

                $result = mysqli_query($conn, "SELECT * FROM users WHERE Email='$email'");
                $user = mysqli_fetch_assoc($result);

                if ($user && password_verify($password, $user['Password'])) {
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['user_name'] = $user['Name'];
                    header("Location: dashboard.php");
                } else {
                    echo "Invalid Email or Password.";
                }
            }
            ?>
        <center>
            <div class="credentials-container">
                <img height="10%" src = "images/favicon.png" alt="Logo" class="logo">
                <h2>Log in</h2>
                <form class="credentials-form" method="POST">
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit">Log in</button>
                </form>
            </div>
        </center>
    </body>
</html>