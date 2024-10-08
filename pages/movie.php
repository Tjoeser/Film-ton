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

$fields = [
    'title' => 'Title',
    'release_date' => 'Release Date',
    'runtime' => 'Runtime',
    'status' => 'Status',
    'adult' => 'Adult Content',
    'vote_average' => 'Vote Average',
    'vote_count' => 'Vote Count',
    'revenue' => 'Revenue',
    'budget' => 'Budget',
    'genres' => 'Genres',
    'popularity' => 'Popularity',
    'production_companies' => 'Production Companies',
    'production_countries' => 'Production Countries',
    'tagline' => 'Tagline',
    'overview' => 'Overview',
    'spoken_languages' => 'Spoken Languages',
    'homepage' => 'Homepage',
    'poster_path' => 'Poster Path',
    // Removed 'imdb_id' field
];

// Initialize values
$values = [];
foreach ($fields as $key => $label) {
    if (isset($response[$key])) {
        // Handle specific formatting for arrays
        if ($key === 'genres' || $key === 'production_companies' || $key === 'production_countries' || $key === 'spoken_languages') {
            // Convert arrays into readable strings
            $values[$key] = implode(", ", array_map(function ($item) {
                return isset($item['name']) ? $item['name'] : 'N/A';
            }, $response[$key]));
        } elseif ($key === 'poster_path') {
            // Format the poster path
            $values[$key] = isset($response[$key]) ? "<img src='https://image.tmdb.org/t/p/w500{$response[$key]}' alt='Poster' class='movie-poster-large'>" : 'N/A';
        } else {
            $values[$key] = $response[$key];
        }
    } else {
        $values[$key] = 'N/A';
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
        <div class="poster">
            <?php echo $values['poster_path']; ?>
        </div>
        <div class="movie-info">
            <p id="movie-info-title"><strong></strong> <?php echo $values['title']; ?></p>
            <p id="movie-info-release_date"> <?php echo $values['release_date']; ?></p>
            <p id="movie-info-tagline"> <?php echo $values['tagline']; ?></p>
            <p id="movie-info-overview"><strong></strong> <?php echo $values['overview']; ?></p>
            <p id="movie-info-genres"><strong>Genres:<br></strong> <?php echo $values['genres']; ?></p>
            <?php echo movieProvidersDisplay($response['id'], "NL");
            ?>
        </div>

        <div id="watchlist-form-container">
            <!-- Forms will be displayed here -->
        </div>

        <div class="additional-info">
            <?php foreach ($fields as $key => $label): ?>
                <?php if ($key !== 'poster_path' && $key !== 'title' && $key !== 'overview' && $key !== 'release_date' && $key !== 'genres' && $values[$key] !== 'N/A'): ?>
                    <p id="movie-info-<?php echo $key; ?>"><strong><?php echo $label; ?>:</strong> <?php echo $values[$key]; ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Get the PHP boolean value
        const isOnWatchlist = <?php echo json_encode($onWatchlist); ?>;

        // Get the container for the forms
        const formContainer = document.getElementById('watchlist-form-container');

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
        displayWatchlistForm();
    </script>
</body>

</html>