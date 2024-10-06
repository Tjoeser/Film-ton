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
        <form id="nav-form">
            <button type="submit" name="page" value="home">Home</button>
            <button type="submit" name="page" value="search">Search</button>
            <button type="submit" name="page" value="login">Login</button>
        </form>
    </header>

    <?php
    require_once 'functions.php'; // Include the functions file
    require_once 'display.php'; // Include the functions file
    include 'misc/config.php';



    if (isset($_GET['movieId'])) {
        $movieId = $_GET['movieId']; // Get the action from the URL
        include 'pages/movie.php';
    }

    if (isset($_GET['page'])) {
        $page = $_GET['page']; // Get the action from the URL

        if ($page === 'home') {
            include 'pages/home.php';
        } elseif ($page === 'search') {
            include 'pages/search.php';
        } elseif ($page === 'login') {
            include 'pages/login.php';
        } elseif ($page === 'account') {
            include 'pages/account.php';
        } else {
            include 'pages/home.php';
        }
    } else {
        // Default action
        include 'pages/home.php';
    }

    ?>
</body>

</html>