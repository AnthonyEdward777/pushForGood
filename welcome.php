<html lang="en">
    <head>
        <?php
            include 'repeated.php';
            SEO();
        ?>
        <title>Welcome - Push For Good</title>
        <link rel="stylesheet" href="styleSheets/welcome.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="nav-left">
                <img width="10%" src="images/favicon.png" alt="Push For Good Logo" class="logo-img">
            </div>

            <ul class="nav-center">
                <li><a href="#">Explore Projects</a></li>
                <li><a href="#">My Dashboard</a></li>
                <li><a href="#">How It Works</a></li>
                <li><a href="#">About</a></li>
            </ul>

            <div class="nav-right">
                <a class="signout-btn" href="login.php">Log in</a>
            </div>
        </nav>

        <center>
            <h1>Welcome to Push For Good!</h1>
            <video style="border-radius: 15px;" width="300" height="300" autoplay muted loop>
                <source src="videos/regSuccess.mp4" type="video/mp4">
            </video> <br>
            <button class="signout-btn">Sign up</button>
        </center>