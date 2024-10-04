<?php


if (isset($_GET['tmId'])) {
    $tmId = htmlspecialchars(trim($_GET['tmId']));
    $response = TMDbID($tmId); // Use the function to get movie details
} else {
    echo "No ID specified.";
}

?>

<body>
    <div class="container" id="page-identifier" data-page="movie">
        <?php
        movieDetailsTMBdDisplay($response);
        ?>
    </div>
</body>

</html>