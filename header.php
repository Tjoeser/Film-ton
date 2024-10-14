<!DOCTYPE html>
<html lang="en">

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
    <link rel="icon" href="misc/media/favicon/1/favicon.ico" type="image/x-icon">

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
    require_once 'datahandler.php'; // Include the functions file
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

<script src="misc/javascript.js"></script>

<script>
window.onload = checkLoginStatus();
</script>