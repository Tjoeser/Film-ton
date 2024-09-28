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
        <form method="post" action="">
            <button type="submit" name="home">Home</button>
            <button type="submit" name="search">Search</button>
        </form>
    </header>

    <?php
    require_once 'functions.php'; // Include the functions file
    require_once 'display.php'; // Include the functions file
    
    if (isset($_POST['home'])) {
        include 'home.php';
    } elseif (isset($_POST['search'])) {
        include 'search.php';
    } elseif (isset($_GET['imdbId'])) {
        include 'movie.php';
    } else {
        include 'home.php';
    }
    ?>
</body>
</html>
