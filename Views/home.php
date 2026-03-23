<html lang="en">
    <head>
        <?php
            include 'repeated.php';
            SEO();
        ?>
        <title>Welcome - Push For Good</title>
        <link rel="stylesheet" href="public/styleSheets/welcome.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="nav-left">
                <img width="10%" src="public/images/favicon.png" alt="Push For Good Logo" class="logo-img">
            </div>
            <div class="nav-right">
                <a class="login-btn" href="Views/auth/login.php">Log in</a>
            </div>
        </nav>

        <center>
            <h1>Welcome to Push For Good!</h1>
            <video style="border-radius: 15px;" width="300" height="300" autoplay muted loop>
                <source src="public/videos/regSuccess.mp4" type="video/mp4">
            </video> <br>
            <a class="signin-btn" href="Views/auth/register.php">Sign up</a>
        </center>