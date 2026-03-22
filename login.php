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

<form method="POST">
    <h2>Login</h2>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>