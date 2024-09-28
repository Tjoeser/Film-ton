<?php


if (isset($_GET['imdbId'])) {
    $imdbId = htmlspecialchars(trim($_GET['imdbId']));
    $response = getMovieDetailsByImdbId($imdbId); // Use the function to get movie details
    $data = json_decode($response, true);
} else {
    echo "No IMDb ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styling.css">
    <title>Movie Details</title>
    <style>
        /* Inline styles for backdrop image */
        body {
            background-image: url('<?php echo $data[0]['backdrop_path']; ?>');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        echo movieDetailsDisplay($data);
        ?>
    </div>
</body>

</html>