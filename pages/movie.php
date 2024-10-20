<?php


if (isset($_GET['movieId'])) {
    $movieId = htmlspecialchars(trim($_GET['movieId']));
    $response = TMDbID($movieId); // Use the function to get movie details
} else {
    echo "No ID specified.";
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_to_watchlist'])) {
        addToWatchlist(); // Call the addToWatchlist function with the movie ID
    }
    if (isset($_POST['remove_from_watchlist'])) {
        removeFromWatchlist(); // Call the addToWatchlist function with the movie ID
    }
}

$cast = getMovieCast($movieId);

$fields = [
    'genres' => 'Genres',
    'production_companies' => 'Production Companies',
    'production_countries' => 'Production Countries',
    'spoken_languages' => 'Spoken Languages',
    'poster_path' => 'Poster Path',
    'origin_country' => 'Origin Country',
];
// Initialize values
$values = [];
foreach ($fields as $key => $label) {
    if (isset($response[$key])) {
        // Handle specific formatting for arrays
        if ($key === 'genres' || $key === 'production_companies' || $key === 'production_countries' || $key === 'spoken_languages' || $key === 'origin_country') {
            $values[$key] = implode(", ", array_map(function ($item) {
                return isset($item['name']) ? $item['name'] : 'N/A';
            }, $response[$key]));
        } elseif ($key === 'poster_path') {
            $values[$key] = isset($response[$key]) ? "<img src='https://image.tmdb.org/t/p/w500{$response[$key]}' alt='Poster' class='movie-poster-large'>" : 'N/A';
        } else {
            $values[$key] = $response[$key];
        }
    } else {
        $values[$key] = 'N/A'; // Default value if not set
    }
}

// Format specific fields
$values['release_date'] = isset($response['release_date']) ? date("F j, Y", strtotime($response['release_date'])) : 'N/A';
$values['revenue'] = isset($response['revenue']) ? '$' . number_format($response['revenue']) : 'N/A';
$values['budget'] = isset($response['budget']) ? '$' . number_format($response['budget']) : 'N/A';
$values['runtime'] = isset($response['runtime']) ? $response['runtime'] . ' mins' : 'N/A';
$values['adult'] = isset($response['adult']) ? ($response['adult'] ? 'Yes' : 'No') : 'N/A';
$values['homepage'] = isset($response['homepage']) ? "<a href='{$response['homepage']}' target='_blank'>Click here</a>" : 'N/A';
$values['tagline'] = isset($response['tagline']) ? "<span class='highlight'>{$response['tagline']}</span>" : 'N/A';


$onWatchlist = isOnWatchlist(); // Call the function and store the boolean result
?>

<body class="moviedetailbody">

    <main class="movie-details">
        <div class="movie-info">
            <div class="poster">
                <?php echo $values['poster_path']; ?>
            </div>
            <div class="movie-details-text">
                <p id="movie-info-title"><strong></strong> <?php echo $response['title']; ?></p>
                <p id="movie-info-release_date"> <?php echo $values['release_date']; ?></p>
                <p id="movie-info-tagline"> <?php echo $values['tagline']; ?></p>
                <p id="movie-info-overview"><strong></strong> <?php echo $response['overview']; ?></p>
                <div class="additional-info-container">
                    <div class="additional-info-left">
                        <h5 class="adinfop">Info</h5>
                        <p id="movie-info-p"><strong>Adult content:<br></strong> <?php echo $values['adult']; ?></p>
                        <p id="movie-info-p"><strong>Genres:<br></strong> <?php echo $values['genres']; ?></p>
                        <?php if ($values['origin_country'] !== 'N/A'): ?>
                            <p id="movie-info-p"><strong>Original country:<br></strong> <?php echo $values['origin_country']; ?></p>
                        <?php endif; ?>
                        <p id="movie-info-p"><strong>Original language:<br></strong> <?php echo $response['original_language']; ?></p>
                        <p id="movie-info-p"><strong>Status:<br></strong> <?php echo $response['status']; ?></p>
                        <p id="movie-info-p"><strong>Release date:<br></strong> <?php echo $values['release_date']; ?></p>

                        <!-- Add any additional info content here -->
                    </div>

                    <div class="additional-info-mid">
                        <h5 class="adinfop">Revenue</h5>
                        <p id="movie-info-p"><strong>Budget:<br></strong>
                            <?php

                            // Check if budget is empty, '0', or 0 and display 'N/A' if true
                            echo (!isset($values['budget']) || $values['budget'] === '' || $values['budget'] === '$0' || $values['budget'] === 0) ? 'N/A' : $values['budget'];
                            ?>
                        </p>
                        <p id="movie-info-p"><strong>Total revenue:<br></strong>
                            <?php
                            // Check if total revenue is empty, '0', or 0 and display 'N/A' if true
                            echo (!isset($values['revenue']) || $values['revenue'] === '' || $values['revenue'] === '$0' || $values['revenue'] === 0) ? 'N/A' : $values['revenue'];
                            ?>
                        </p>


                        <?php
                        // Calculate the total difference
                        $budgetValue = str_replace(['$', ','], '', $values['budget']); // Remove $ and commas for calculation
                        $revenueValue = str_replace(['$', ','], '', $values['revenue']); // Remove $ and commas for calculation

                        // Default to N/A if either budget or revenue is zero
                        if ($budgetValue == 0 || $revenueValue == 0) {
                            $difference = null; // Set difference to null
                        } else {
                            $difference = (float)$revenueValue - (float)$budgetValue; // Calculate the difference
                        }
                        ?>

                        <p id="movie-info-p"><strong>
                                <?php
                                if ($difference === null) {
                                    echo 'Total Profit:<br>'; // Change title for loss
                                } else if ($difference < 0) {
                                    echo 'Total Loss:<br>'; // Change title for loss
                                } else {
                                    echo 'Total Profit:<br>'; // Change title for profit
                                }
                                ?>
                            </strong>
                            <span style="color: <?php echo $difference === null ? 'white' : ($difference < 0 ? 'red' : 'green'); ?>;">
                                <?php
                                if ($difference === null) {
                                    echo 'N/A'; // Display N/A if difference is null
                                } else {
                                    echo '$' . number_format(abs($difference)); // Show absolute value
                                }
                                ?>
                            </span>
                        </p>
                    </div>

                    <div class="additional-info-right">
                        <h5 class="adinfop">Review</h5>
                        <p id="movie-info-p"><strong>Vote count:<br></strong>
                            <?php
                            echo (!empty($response['vote_count']) && $response['vote_count'] != 0) ? $response['vote_count'] : 'N/A'; // Display N/A if vote count is 0 or empty
                            ?>
                        </p>
                        <p id="movie-info-p"><strong>Vote average:<br></strong>
                            <?php
                            echo (!empty($response['vote_average']) && $response['vote_average'] != 0) ? $response['vote_average'] : 'N/A'; // Display N/A if vote average is 0 or empty
                            ?>
                        </p>
                        <p id="movie-info-p"><strong>Film-ton Rating:<br></strong> <?php echo "TBD"; ?></p>
                    </div>

                </div>
            </div>
            <div id="watchlist-form-container">
                <div id="watchlist-form">
                </div>
                <div id="providers-div">
                    <?php echo movieProvidersDisplay($response['id'], pullSpecificAccountDataDatahandler("countrycode")); ?>
                </diV>
            </div>
        </div>



        <div class="cast-crew">
            <?php
            // Extract the year from the release date
            $year = date("Y", strtotime($values['release_date']));
            $movieTitle = htmlspecialchars($response['title']);
            $searchQuery = urlencode("{$movieTitle} {$year}");
            ?>
            <a href="https://www.google.com/search?q=<?php echo $searchQuery; ?>" target="_blank">google search this movie</a>
            <p class="adinfop">Cast and credits</p>
            <?php
            foreach ($cast as $member) {
                echo '<a href="?page=actor&actorId=' . urlencode(htmlspecialchars($member['id'])) . '" class="actor">' . htmlspecialchars($member['name']) . '</a>' . ' , ';
                echo '<a href="https://www.google.com/search?q=' . urlencode(htmlspecialchars($member['character'])) . '" class="character">' . htmlspecialchars($member['character']) . '</a>' . ' | ';
            }
            ?>
        </div>

    </main>



    <script>
        // Get the PHP boolean value
        const isOnWatchlist = <?php echo json_encode($onWatchlist); ?>;

        // Get the container for the forms
        const formContainer = document.getElementById('watchlist-form');

        // Function to display the appropriate form based on watchlist status
        function displayWatchlistForm() {
            if (isOnWatchlist) {
                formContainer.innerHTML = `
                    <form method="POST" action="">
                        <button type="submit" name="remove_from_watchlist">Remove from Watchlist</button>
                    </form>`;
            } else {
                formContainer.innerHTML = `
                    <form method="POST" action="">
                        <button type="submit" name="add_to_watchlist">Add to Watchlist</button>
                    </form>`;
            }
        }

        // Run the function on page load
        // Get the PHP boolean value of isUserLoggedIn
        const isUserLoggedIn = <?php echo json_encode(isUserLoggedIn()); ?>;

        // Run the function on page load if the user is logged in
        if (isUserLoggedIn) {
            displayWatchlistForm();
        }
    </script>
</body>

</html>