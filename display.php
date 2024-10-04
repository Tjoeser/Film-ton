<?php

function searchDisplay($data)
{
    echo "<div class='movie-grid'>"; // Regular grid layout for search results

    foreach ($data['results'] as $movie) {
        $title = htmlspecialchars($movie['original_title'] ?? 'N/A');
        $posterPath = !empty($movie['poster_path'])
        ? "https://image.tmdb.org/t/p/w500" . htmlspecialchars($movie['poster_path'])
        : 'default-poster.jpg'; // Default image for missing poster
        $tmId = htmlspecialchars($movie['id'] ?? ''); // Ensure IMDb ID is available

        echo "<div class='movie-card'>";
        echo "<a href='?tmId=$tmId'>"; // Use GET parameter to pass movie ID
        echo "<img src='$posterPath' alt='Poster' class='movie-poster'>";
        echo "<h4 class='movie-title'>$title</h4>";
        echo "</a>";
        echo "</div>";
    }

    echo "</div>";
}

function scrollableMoviesTMDbDisplay($movies)
{
    // Check if 'results' key exists in the $movies array
    if (!isset($movies['results']) || !is_array($movies['results'])) {
        echo "No movies available.";
        return;
    }

    echo "<div class='scrollable-container'>"; // Wrapper for horizontal scrolling

    foreach ($movies['results'] as $movie) {
        // Extract title, poster path, and ID safely
        $title = htmlspecialchars($movie['original_title'] ?? 'Untitled');
        $posterPath = !empty($movie['poster_path'])
            ? "https://image.tmdb.org/t/p/w500" . htmlspecialchars($movie['poster_path'])
            : 'default-poster.jpg'; // Default image for missing poster
        $tmId = htmlspecialchars($movie['id']); // Always present

        // Build the movie card
        echo "<div class='movie-card'>";
        echo "<a href='?tmId=$tmId'>"; // Link to details page
        echo "<img src='$posterPath' alt='Poster of $title' class='movie-poster'>";
        echo "<h4 class='movie-title'>$title</h4>";
        echo "</a>";
        echo "</div>";
    }

    echo "</div>"; // Close the scrollable container
}

function movieDetailsTMBdDisplay($data)
{
    // Check if the data is an array and not empty
    if (is_array($data) && !empty($data)) {
        // Define the fields and their labels
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
            if (isset($data[$key])) {
                // Handle specific formatting for arrays
                if ($key === 'genres' || $key === 'production_companies' || $key === 'production_countries' || $key === 'spoken_languages') {
                    // Convert arrays into readable strings
                    $values[$key] = implode(", ", array_map(function ($item) {
                        return isset($item['name']) ? $item['name'] : 'N/A';
                    }, $data[$key]));
                } elseif ($key === 'poster_path') {
                    // Format the poster path
                    $values[$key] = isset($data[$key]) ? "<img src='https://image.tmdb.org/t/p/w500{$data[$key]}' alt='Poster' class='movie-poster-large'>" : 'N/A';
                } else {
                    $values[$key] = $data[$key];
                }
            } else {
                $values[$key] = 'N/A';
            }
        }

        // Format specific fields
        $values['release_date'] = isset($data['release_date']) ? date("F j, Y", strtotime($data['release_date'])) : 'N/A';
        $values['revenue'] = isset($data['revenue']) ? '$' . number_format($data['revenue']) : 'N/A';
        $values['budget'] = isset($data['budget']) ? '$' . number_format($data['budget']) : 'N/A';
        $values['runtime'] = isset($data['runtime']) ? $data['runtime'] . ' mins' : 'N/A';
        $values['adult'] = isset($data['adult']) ? ($data['adult'] ? 'Yes' : 'No') : 'N/A';
        $values['homepage'] = isset($data['homepage']) ? "<a href='{$data['homepage']}' target='_blank'>Click here</a>" : 'N/A';
        $values['tagline'] = isset($data['tagline']) ? "<span class='highlight'>{$data['tagline']}</span>" : 'N/A';

    ?>
        <div class="movie-details" >
            <div class="poster">
                <?php echo $values['poster_path']; ?>
            </div>
            <div class="movie-info">
                <p id="movie-info-title"><strong></strong> <?php echo $values['title']; ?></p>
                <p id="movie-info-release_date"> <?php echo $values['release_date']; ?></p>
                <p id="movie-info-overview"><strong>Overview:</strong> <?php echo $values['overview']; ?></p>
                <p id="movie-info-genres"><strong>Genres:</strong> <?php echo $values['genres']; ?></p>
                <?php echo movieDetailsWithProvidersDisplay($data['id'], "NL"); ?>
                </div>


                <div class="additional-info">
                    <?php foreach ($fields as $key => $label): ?>
                        <?php if ($key !== 'poster_path' && $key !== 'title' && $key !== 'overview' && $key !== 'release_date' && $key !== 'genres' && $values[$key] !== 'N/A'): // Skip the main fields and those with 'N/A' 
                        ?>
                            <p id="movie-info-<?php echo $key; ?>"><strong><?php echo $label; ?>:</strong> <?php echo $values[$key]; ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
        </div>
<?php
    } else {
        echo "<p>No details found for this movie.</p>";
    }
}



function movieDetailsWithProvidersDisplay($movieId, $countryCode)
{
    // Get watch providers
    $providers = getMovieWatchProviders($movieId, $countryCode);

    // Display the providers
    if (!empty($providers['flatrate'])) {
        echo "<div class='provider-available'>"; // New wrapper for styling
        echo "<h3>Available on:</h3>";
        echo "<div class='provider-logos'>"; // New wrapper for styling
        foreach ($providers['flatrate'] as $provider) {
            if (isset($provider['logo_path'])) {
                echo "<img src='https://image.tmdb.org/t/p/w500{$provider['logo_path']}' alt='{$provider['provider_name']}' class='provider-logo'>";
            }
        }
        echo "</div>"; // Closing wrapper
    } else {
        echo "<p>This movie is not available on any streaming service in your country.</p>";
    }
    echo "</div>"; // Closing wrapper

}

