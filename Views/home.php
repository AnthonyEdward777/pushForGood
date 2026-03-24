<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= basePath() ?>/public/images/favicon.png">
    <title>Welcome - Push For Good</title>
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/welcome.css">
    <link rel="stylesheet" href="<?= basePath() ?>/public/stylesheets/home_page.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-left">
            <img src="public/images/favicon.png" alt="Push For Good Logo" class="logo-img">
        </div>
        <div class="nav-right">
            <a class="login-btn" href="<?= basePath() ?>/login">Log in</a>
        </div>
    </nav>

    <main class="home-hero">
        <h1>Welcome to Push For Good!</h1>
        <video class="hero-video" autoplay muted loop>
            <source src="public/videos/regSuccess.mp4" type="video/mp4">
        </video>
        <a class="signin-btn" href="<?= basePath() ?>/register">Sign up</a>
    </main>
</body>

</html>