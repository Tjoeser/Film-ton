<?php

function searchDisplay($data)
{
    echo "<div class='movie-grid'>"; // Regular grid layout for search results

    foreach ($data as $item) {
        $title = htmlspecialchars($item['title'] ?? 'N/A');
        $posterPath = isset($item['poster_path']) ? $item['poster_path'] : 'N/A';
        $imdbId = htmlspecialchars($item['imdb_id'] ?? ''); // Ensure IMDb ID is available

        echo "<div class='movie-card'>";
        echo "<a href='?imdbId=$imdbId'>"; // Use GET parameter to pass movie ID
        echo "<img src='$posterPath' alt='Poster' class='movie-poster'>";
        echo "<h4 class='movie-title'>$title</h4>";
        echo "</a>";
        echo "</div>";
    }

    echo "</div>";
}

function movieDetailsDisplay($data)
{
    if (is_array($data) && !empty($data)) {
        $item = $data[0]; // Simplify access to the first item

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
            'homepage' => 'IMDb Page',
            'poster_path' => 'Poster Path',
        ];

        // Initialize values
        $values = [];
        foreach ($fields as $key => $label) {
            $values[$key] = isset($item[$key]) ? $item[$key] : 'N/A';
        }

        // Handle special formatting
        $values['release_date'] = isset($item['release_date']) ? date("F j, Y", strtotime($item['release_date'])) : 'N/A';
        $values['revenue'] = isset($item['revenue']) ? number_format($item['revenue']) : 'N/A';
        $values['budget'] = isset($item['budget']) ? number_format($item['budget']) : 'N/A';
        $values['runtime'] = isset($item['runtime']) ? $item['runtime'] . ' mins' : 'N/A';
        $values['adult'] = isset($item['adult']) ? ($item['adult'] ? 'Yes' : 'No') : 'N/A';
        $values['homepage'] = isset($item['homepage']) ? "<a href='{$item['homepage']}' target='_blank'>Click here</a>" : 'N/A';
        $values['poster_path'] = isset($item['poster_path']) ? "<img src='{$item['poster_path']}' alt='Poster' class='movie-poster-large'>" : 'N/A';
        $values['tagline'] = isset($item['tagline']) ? "<span class='highlight'>{$item['tagline']}</span>" : 'N/A';

        // Debugging: Output the raw data
        echo '<pre>';
        print_r($item['adult']);
        echo '</pre>';

        ?>
        <div class="movie-details">
            <?php echo $values['poster_path']; ?>
            <div class="movie-info">
                <?php foreach ($fields as $key => $label): ?>
                    <?php if ($key !== 'poster_path'): // Skip the poster_path field as it's already handled ?>
                        <p><strong><?php echo $label; ?>:</strong> <?php echo $values[$key]; ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    } else {
        echo "<p>No details found for this movie.</p>";
    }
}


function scrollMovieDisplay($data)
{
    echo "<div class='scrollable-container'>"; // Horizontal scrolling layout for specific displays

    foreach ($data as $item) {
        $title = htmlspecialchars($item['title'] ?? 'N/A');
        $posterPath = isset($item['poster_path']) ? $item['poster_path'] : 'N/A';
        $imdbId = htmlspecialchars($item['imdb_id'] ?? ''); // Ensure IMDb ID is available

        echo "<div class='movie-card'>";
        echo "<a href='?imdbId=$imdbId'>"; // Use GET parameter to pass movie ID
        echo "<img src='$posterPath' alt='Poster' class='movie-poster'>";
        echo "<h4 class='movie-title'>$title</h4>";
        echo "</a>";
        echo "</div>";
    }

    echo "</div>";
}
