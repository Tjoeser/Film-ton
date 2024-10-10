<?php

function GetMoviesByTitle($title)
{
    // TMDb API Key
    $apiKey = apiKey;
    // Movie or TV Show to search
    $query = urlencode($title);

    // TMDb API URL for searching a movie
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$apiKey}&query={$query}";

    // Initialize CURL
    $response = file_get_contents($url);

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    // Get movie ID from the search results
    $movieId = $data['results'][0]['id'] ?? null;

    if ($movieId) {
        echo "<h2>" . $title . "</h2>";
        searchDisplay($data);
        // displayScrollableMoviesTMDb($data);
    } else {
        echo "<br>Search something.<br><br>";
    }
}

function getRandomMovie()
{
    $apiKey = '62f2c485f5b675bdef3f30d6df52ea62';
    $totalPages = 10; // Max number of pages TMDb allows
    $randomPage = rand(1, $totalPages);

    // Build the URL with the random page
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language=en-US&sort_by=popularity.desc&page={$randomPage}";

    // Fetch the movie data
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if there are results
    if (isset($data['results']) && !empty($data['results'])) {
        // Select a random movie from the results
        $movies = $data['results'];
        $randomMovie = $movies[array_rand($movies)];
        scrollableMoviesTMDbDisplay($data);
        return $randomMovie;
    }

    return null;
}

function getMoviesByGenre($genreId, $page = 1)
{
    $apiKey = apiKey;
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language=en-US&sort_by=popularity.desc&with_genres={$genreId}&page={$page}";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);
    // var_dump($data);

    if ($response === FALSE) {
        die('Error occurred while fetching movies by genre.');
    } else {
        scrollableMoviesTMDbDisplay($data);
    }
}

function getMovieById($movieId)
{
    // TMDb API Key
    $apiKey = apiKey; // Replace with your TMDb API key

    // TMDb API URL for fetching a movie by ID
    $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}";

    // Initialize CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for CURL errors
    if (curl_errno($ch)) {
        echo 'CURL Error: ' . curl_error($ch);
        curl_close($ch);
        return; // Skip this ID if there's a CURL error
    }

    curl_close($ch);

    // Decode the JSON response into an associative array
    $movieDetails = json_decode($response, true);

    // Check for errors in the response or if no movie is found
    if (isset($movieDetails['success']) && $movieDetails['success'] === false) {
        // Log or handle the error as needed
        return; // Skip this ID if there's an error
    }

    // Display the movie details only if valid movie details are returned
    if (!empty($movieDetails) && isset($movieDetails['id'])) {
        return $movieDetails;
    } else {
        // Skip this ID if no movie found
        return;
    }

    return $movieDetails; // Return the movie details (if needed)
}





function TMDbID($movieId)
{
    $apiKey = apiKey;

    $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=en-US";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Check if the response is valid
    if ($response === FALSE) {
        die('Error occurred while fetching movie details.');
    }

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    return $data;
}

function getMovieWatchProviders($movieId, $countryCode)
{
    $apiKey = apiKey;
    $url = "https://api.themoviedb.org/3/movie/{$movieId}/watch/providers?api_key={$apiKey}";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Check if the response is valid
    if ($response === FALSE) {
        die('Error occurred while fetching watch providers.');
    }

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    // Return the watch providers for the specified country
    return isset($data['results'][$countryCode]) ? $data['results'][$countryCode] : [];
}


function registerAccount($email, $password, $countryCode)
{
    registerAccountDatahandler($email, $password, $countryCode);
}



function loginAccount($email, $password)
{
    if (loginAccountDatahandler($email, $password)) {
        setcookie('loggedin', 'true', time() + 3600, '/'); // Indicate user is logged in
        header("Location: ?page=login"); // Redirect to the specific page
        exit(); // Exit to ensure no further code is executed
    }
}


function updateAccount() {}

function logoutAccount()
{
    // Check if the user_id cookie exists
    if (isset($_COOKIE['user_id'])) {
        // Retrieve the user_id cookie value
        $userId = $_COOKIE['user_id'];

        // Unset the cookie to log the user out
        setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
        setcookie('loggedin', 'false', time() - 3600, '/'); // Indicate user is logged out

        echo "User with ID $userId has logged out.";
        header("Location: ?page=login"); // Redirect to the specific page
        exit(); // Ensure no further code is executed
    } else {
        echo "No user is currently logged in.";
    }
}

function deleteAccount()
{
    // Check if the user_id cookie exists
    if (isset($_COOKIE['user_id'])) {
        // Retrieve the user_id cookie value
        $userId = $_COOKIE['user_id'];

        // Define the path to the JSON file
        $filePath = './misc/userdata.json';

        // Check if the file exists
        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $userData = json_decode($jsonData, true);

            // Find the user by their ID and remove the entry
            foreach ($userData as $key => $user) {
                if ($user['id'] == $userId) {
                    // Remove the user entry from the array
                    unset($userData[$key]);
                    break; // Exit the loop once the user is found and removed
                }
            }

            // Save the updated user data back to the JSON file
            file_put_contents($filePath, json_encode(array_values($userData), JSON_PRETTY_PRINT));

            // Unset the cookie to log the user out
            setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
            setcookie('loggedin', 'false', time() - 3600, '/'); // Indicate user is logged out

            echo "User with ID $userId has been deleted.";
            header("Location: ?page=login"); // Redirect to the specific page
            exit(); // Ensure no further code is executed
        } else {
            echo "User data file not found.";
        }
    } else {
        echo "No user is currently logged in.";
    }
}


function pullWatchlist()
{
    $data = pullWatchlistDatahandler($_COOKIE['user_id']); // Retrieve watchlist data

    // Initialize an array to hold all movie details
    $allMovieDetails = [];

    if ($data && is_array($data)) {
        foreach ($data as $entry) {
            if (isset($entry['movieid'])) {
                $movieDetails = getMovieById($entry['movieid']);

                // Check if movie details are not null before adding to the array
                if ($movieDetails) {
                    $allMovieDetails[] = $movieDetails; // Add movie details to the array
                }
            }
        }

        // Pass the collected movie details array to searchDisplay
        if (!empty($allMovieDetails)) {
            searchDisplay(['results' => $allMovieDetails]); // Assuming searchDisplay expects an array with a 'results' key
        } else {
            echo "No movies found in the watchlist.";
        }
    } else {
        echo "No entries found in the watchlist.";
    }
}


function addToWatchlist()
{
    $userId = $_COOKIE['user_id'];
    $movieId = $_GET['movieId'];
    addToWatchlistDatahandler($userId, $movieId);
}

function removeFromWatchlist()
{
    $userId = $_COOKIE['user_id'];
    $movieId = $_GET['movieId'];
    removeFromWatchlistDatahandler($userId, $movieId);
}

function isOnWatchlist()
{
    $userId = $_COOKIE['user_id'];
    $movieId = $_GET['movieId'];
    return isOnWatchlistDatahandler($userId, $movieId);
}

function getWatchProvidersInCountry()
{
    $countryCode = pullSpecificAccountDataDatahandler('countrycode');
    $apiKey = apiKey; // Replace with your actual API key
    $url = "https://api.themoviedb.org/3/watch/providers/movie?api_key={$apiKey}&language=en-US&watch_region={$countryCode}";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Check if the response is valid
    if ($response === FALSE) {
        die('Error occurred while fetching watch providers.');
    }

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    // Collect all providers in the specified country
    $availableProviders = [];

    if (isset($data['results'])) {
        foreach ($data['results'] as $provider) {
            $availableProviders[] = $provider['provider_name'];
        }
    }

    return $availableProviders;
}
