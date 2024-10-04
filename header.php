<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styling.css">
    <title>Film Ton</title>
</head>
<body>
    <header>
        <h1>Film Ton</h1>
        <form method="post" action="" id="nav-form">
            <button type="submit" id="home" name="home">Home</button>
            <button type="submit" id="search" name="search">Search</button>
            <button type="submit" id="login" name="login">Login</button>
        </form>
    </header>

    <?php
    require_once 'functions.php'; // Include the functions file
    require_once 'display.php'; // Include the functions file
    include 'misc/config.php';
    

    if (isset($_POST['home'])) {
        include 'pages/home.php';
    } elseif (isset($_POST['search'])) {
        include 'pages/search.php';
    } elseif (isset($_GET['tmId'])) {
        include 'pages/movie.php';
    } elseif (isset($_POST['login'])) {
        include 'pages/login.php';
    }else {
        include 'pages/home.php';
    }
    ?>
</body>
</html>
