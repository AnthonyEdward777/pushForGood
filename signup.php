<html lang="en">
    <head>
        <?php
            include 'repeated.php';
            SEO();
        ?>
        <title>Sign Up - Push For Good</title>
        <link rel="stylesheet" href="styleSheets/credentials.css">
    </head>
    <body>
        <center>
            <div class="credentials-container">
                <img height="10%" src = "images/favicon.png" alt="Logo" class="logo">
                <h2>Sign Up</h2>
                <form class="credentials-form" method="POST">
                    <input type="text" name="name" placeholder="Full Name" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit">Register</button>
                </form>
            </div>
        </center>
    


<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (Name, Email, Password) VALUES ('$name', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        header("Location: regSuccess.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
    </body>
</html>