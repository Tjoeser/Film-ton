<?php


function simpleMovieDisplay($movie)
{

    $title = htmlspecialchars($movie['original_title'] ?? 'Untitled');
    $posterPath = !empty($movie['poster_path'])
        ? "https://image.tmdb.org/t/p/w500" . htmlspecialchars($movie['poster_path'])
        : 'default-poster.jpg'; // Default image for missing poster
    $movieId = htmlspecialchars($movie['id']); // Always present

    // Build the movie card
    echo "<div class='movie-card'>";
    echo "<a href='?movieId=$movieId'>"; // Link to details page
    echo "<img src='$posterPath' alt='Poster of $title' class='movie-poster'>";
    // echo "<h4 class='movie-title'>$title</h4>";
    echo "</a>";
    echo "</div>";
}

function searchDisplay($data)
{
    // Check if the data is not null and contains results or cast
    if ((isset($data['results']) && is_array($data['results']) && count($data['results']) > 0) ||
        (isset($data['cast']) && is_array($data['cast']) && count($data['cast']) > 0)) {
        
        echo "<div class='movie-grid'>"; // Regular grid layout for search results

        // Check for movie results
        if (isset($data['results'])) {
            foreach ($data['results'] as $movie) {
                // Skip if poster_path is not available or empty
                if (empty($movie['poster_path'])) {
                    continue; // Skip this iteration and don't display the movie
                }
                simpleMovieDisplay($movie); // Call your display function
            }
        }

        // Check for actor cast
        if (isset($data['cast'])) {
            foreach ($data['cast'] as $actor) {
                // Skip if poster_path is not available or empty
                if (empty($actor['poster_path'])) {
                    continue; // Skip this iteration and don't display the actor
                }
                // Here you might want to call a different function if the display is different for cast
                simpleMovieDisplay($actor); // Or create a new function for actor display if necessary
            }
        }

        echo "</div>";
    } else {
        // Optionally, display a message if no results are found
        echo "<p>No movies or cast found.</p>";
    }
}



require_once 'functions.php'; // Adjust the path as necessary

function scrollableMoviesTMDbDisplay($movies)
{
    // Check if 'results' key exists in the $movies array
    if (!isset($movies['results']) || !is_array($movies['results'])) {
        echo "No movies available.";
        return;
    }

    echo "<div class='scrollable-container'>"; // Wrapper for horizontal scrolling

    foreach ($movies['results'] as $movie) {
        simpleMovieDisplay($movie);
    }

    echo "</div>"; // Close the scrollable container
}


function movieProvidersDisplay($movieId, $countryCode)
{
    // Check if the user is logged in
    if (!isUserLoggedIn()) {
        // Display a message prompting the user to log in
        echo "<div class='login-message'>";
        echo "<h4>Please <a href='?page=login'>log in</a> to see available streaming services or to add this movie to your watchlist.</h4>";
        echo "</div>";
        return; // Exit the function if not logged in
    }

    // Get watch providers from the user account (assumed to return a JSON string)
    $ownedProvidersJson = pullSpecificAccountDataDatahandler('streaming');
    $ownedProviders = json_decode($ownedProvidersJson, true); // Decode JSON to array

    // Ensure the owned providers are an array and split the comma-separated string
    if (!is_array($ownedProviders)) {
        $ownedProviders = [];
    }

    // Normalize and split owned providers into an array, then convert to lowercase and trim
    $ownedProviders = array_map(function ($provider) {
        return strtolower(trim($provider));
    }, explode(',', $ownedProviders[0])); // Split the string into an array

    // Get movie watch providers for the current movie
    $providers = getMovieWatchProviders($movieId, $countryCode);

    $ownedProviderLogos = [];
    $otherProviderLogos = [];

    // Organize providers into 'owned' and 'other'
    if (!empty($providers['flatrate'])) {
        foreach ($providers['flatrate'] as $provider) {
            if (isset($provider['logo_path'])) {
                // Normalize and trim the provider name to lowercase for comparison
                $providerName = strtolower(trim($provider['provider_name']));
                // Check if the provider is in the ownedProviders array
                if (in_array($providerName, $ownedProviders)) {
                    $ownedProviderLogos[] = $provider; // Add to owned providers list
                } else {
                    $otherProviderLogos[] = $provider; // Add to other providers list
                }
            }
        }
    }

    // Initialize flags for provider availability
    $noneOwned = true; // Flag for owned providers
    $noneNotOwned = true; // Flag for other providers

    // Function to display provider logos
    function displayProviders($providerLogos, $heading, $noneFlag)
    {
        if (!empty($providerLogos)) {
            echo "<div class='provider-available'>";
            echo "<h4>{$heading}</h4>";
            echo "<div class='provider-logos'>";
            foreach ($providerLogos as $provider) {
                echo "<img src='https://image.tmdb.org/t/p/w500{$provider['logo_path']}' alt='{$provider['provider_name']}' class='provider-logo'>";
            }
            echo "</div>"; // Close provider-logos
            echo "</div>"; // Close provider-available
            return false; // Set noneFlag to false as providers are available
        }
        return $noneFlag; // Return the current state of noneFlag
    }

    // Display the owned providers
    $noneOwned = displayProviders($ownedProviderLogos, "Available on your owned streaming services:", $noneOwned);

    // Display the other providers
    $noneNotOwned = displayProviders($otherProviderLogos, "Other services you do not own:", $noneNotOwned);

    // Final check for streaming service availability
    if ($noneOwned && $noneNotOwned) {
        echo "<div class='provider-available'>";
        echo "<h4>This movie is not available on any streaming services in your country.</h4>";
        echo "</div>";
    }
}





function registrationForm()
{
    echo '<h1>Register</h1>';
    echo '<form id="registration-form" class="form" method="POST" action="?page=login">';

    echo '<label for="email">Email:</label>';
    echo '<input type="text" class="form-input" id="email" name="email" value="" required><br><br>';

    echo '<label for="password">Password:</label>';
    echo '<input type="password" class="form-input" id="password" name="password" value="" required><br><br>';

    echo '<label for="country">Country:</label>';
    echo '<select name="country" id="country" required>';
    echo '<option value="AD">Andorra</option>';
    echo '<option value="AR">Argentina</option>';
    echo '<option value="AT">Austria</option>';
    echo '<option value="AU">Australia</option>';
    echo '<option value="BE">Belgium</option>';
    echo '<option value="BR">Brazil</option>';
    echo '<option value="CA">Canada</option>';
    echo '<option value="CH">Switzerland</option>';
    echo '<option value="CL">Chile</option>';
    echo '<option value="CO">Colombia</option>';
    echo '<option value="CZ">Czech Republic</option>';
    echo '<option value="DE">Germany</option>';
    echo '<option value="DK">Denmark</option>';
    echo '<option value="EE">Estonia</option>';
    echo '<option value="ES">Spain</option>';
    echo '<option value="FI">Finland</option>';
    echo '<option value="FR">France</option>';
    echo '<option value="GB">United Kingdom</option>';
    echo '<option value="GR">Greece</option>';
    echo '<option value="HK">Hong Kong</option>';
    echo '<option value="HR">Croatia</option>';
    echo '<option value="HU">Hungary</option>';
    echo '<option value="IE">Ireland</option>';
    echo '<option value="IL">Israel</option>';
    echo '<option value="IN">India</option>';
    echo '<option value="IT">Italy</option>';
    echo '<option value="JP">Japan</option>';
    echo '<option value="KR">South Korea</option>';
    echo '<option value="LT">Lithuania</option>';
    echo '<option value="LU">Luxembourg</option>';
    echo '<option value="LV">Latvia</option>';
    echo '<option value="MX">Mexico</option>';
    echo '<option value="MY">Malaysia</option>';
    echo '<option value="NL">Netherlands</option>';
    echo '<option value="NO">Norway</option>';
    echo '<option value="NZ">New Zealand</option>';
    echo '<option value="PE">Peru</option>';
    echo '<option value="PH">Philippines</option>';
    echo '<option value="PL">Poland</option>';
    echo '<option value="PT">Portugal</option>';
    echo '<option value="RO">Romania</option>';
    echo '<option value="RS">Serbia</option>';
    echo '<option value="RU">Russia</option>';
    echo '<option value="SE">Sweden</option>';
    echo '<option value="SG">Singapore</option>';
    echo '<option value="TH">Thailand</option>';
    echo '<option value="TR">Turkey</option>';
    echo '<option value="US">United States</option>';
    echo '<option value="ZA">South Africa</option>';
    echo '</select><br><br>';

    echo '<input type="submit" class="form-button" id="register" name="register" value="Register"><br><br>';
    echo '</form>';
}


function loginForm($email, $password)
{
    echo '<h1>Login</h1>';
    echo '<form id="login-form" class="form" method="POST" action="?page=login">';
    echo '<label for="email">Email:</label>';
    echo '<input type="text" class="form-input" id="email" name="email" value="' . $email . '" required><br><br>';
    echo '<label for="password">Password:</label>';
    echo '<input type="password" class="form-input" id="password" name="password" value="' . $password . '" required><br><br>';
    echo '<input type="submit" class="form-button" id="login" name="login" value="Login"><br><br>';
    echo '</form>';
}
