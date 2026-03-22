<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
<p>This is your private software dashboard.</p>
<a href="logout.php">Logout</a>
