<!DOCTYPE html>
<html lang="en">

<?php
$live = false;

if (strpos($_SERVER['HTTP_HOST'], 'thefilmton') !== false) {
    $live = true;
}
define('LIVE_MODE', $live);
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Film Ton: A movie watchlist website that helps you track and find where to stream your favorite films.">
    <meta name="author" content="Thijs Rietveld">
    <meta name="keywords" content="movies, watchlist, streaming, Film Ton, cinema, films">

    <!-- Link to Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap">

    <!-- Link to your main CSS stylesheet -->
    <link rel="stylesheet" href="css/styling.css">

    <!-- Favicon -->
    <link rel="icon" href="misc/media/png/bare_logo.png" type="image/x-icon">

    <?php global $live;
    $title = ($live === true) ? "The Film Ton" : "Film Ton"; ?>
    <title><?php echo $title; ?></title>
</head>


<body>
    <header>
        <h1> The Film Ton</h1>
        <p class="slogan">Search, Save, Stream</p>
        <form id="nav-form">
            <button type="submit" name="page" value="home">Home</button>
            <button type="submit" name="page" value="search">Search</button>
            <button type="submit" name="page" value="account">Account</button>
        </form>
    </header>

    <?php
    require_once 'functions.php'; // Include the functions file
    require_once 'display.php'; // Include the functions file
    require_once 'datahandler.php'; // Include the functions file
    include 'misc/config.php'; // Include the config file



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
        } elseif ($page === 'actor') {
            include 'pages/actor.php';
        }  else {
            include 'pages/home.php';
        }
    } else {
        // Default action
        include 'pages/home.php';
    }

    ?>
</body>

</html>

<script src="misc/javascript.js"></script>

<script>
    window.onload = checkLoginStatus();
</script>