<?php
require 'misc/config.php'; // or include 'config.php';
session_start();


function GetMoviesByTitle($title)
{
    $apiKey = apiKey;
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
    $apiKey = apiKey;
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

function getMoviesByGenre($genreId, $home, $page = 1)
{
    $apiKey = apiKey;
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language=en-US&sort_by=popularity.desc&with_genres={$genreId}&page={$page}";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Check for errors
    if ($response === FALSE) {
        die('Error occurred while fetching movies by genre.');
    }

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    // Depending on the home parameter, call different display functions
    if ($home) {
        scrollableMoviesTMDbDisplay($data);
    } else {
        searchDisplay($data);
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
            // Capitalize provider names to match getMovieWatchProviders format
            $availableProviders[] = ucfirst(strtolower($provider['provider_name']));
        }
    }

    return $availableProviders;
}




function registerAccount($email, $password, $countryCode)
{
    registerAccountDatahandler($email, $password, $countryCode);
}



function loginAccount($email, $password)
{
    if (loginAccountDatahandler($email, $password)) {
        setcookie('loggedin', 'true', time() + (30 * 24 * 60 * 60), '/'); // Indicate user is logged in for 30 days
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
    if (isset($_COOKIE['user_id'])) {
        $userId = $_COOKIE['user_id'];
        $movieId = $_GET['movieId'];
        return isOnWatchlistDatahandler($userId, $movieId);
    } else {
        // Handle case when user_id is not set
        return false; // Or some other logic to handle this case
    }
}


function addStreamingServices($services)
{
    addStreamingServicesDatahandler($services);
}

function getMovieCast($movieId)
{
    // Debug: Check the received movie ID
    $apiKey = apiKey; // Make sure to replace this with your actual API key
    $url = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=en-US";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Check if the response is valid
    if ($response === FALSE) {
        die('Error occurred while fetching movie credits.');
    }

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);


    // Check if 'cast' key exists within 'credits'
    if (isset($data['cast'])) {
        return $data['cast']; // Return the cast information
    } else {
        echo "No cast information found.";
        return []; // Return an empty array if no cast information is found
    }
}

function isUserLoggedIn()
{
    return isset($_COOKIE['loggedin']); // or the session variable you use
}

function getActorFullDetails($actorId)
{
    // Your TMDb API key
    $apiKey = apiKey;

    // URLs for fetching actor info and movie credits
    $actorInfoUrl = "https://api.themoviedb.org/3/person/{$actorId}?api_key={$apiKey}&language=en-US";
    $movieCreditsUrl = "https://api.themoviedb.org/3/person/{$actorId}/movie_credits?api_key={$apiKey}&language=en-US";

    // Function to fetch data from the API
    function fetchData($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    // Fetch actor info
    $actorInfo = fetchData($actorInfoUrl);

    // Fetch actor's movie credits
    $movieCredits = fetchData($movieCreditsUrl);

    // Return both actor info and movie credits
    if (isset($actorInfo['id'])) {
        return [
            'actorInfo' => $actorInfo,
            'movieCredits' => $movieCredits
        ];
    } else {
        return "Error: Unable to fetch actor details";
    }
}

// Ensure the constant is defined before any function calls

function GetMovieSuggestions($query)
{
    $apiKey = apiKey; // Use the constant
    $apiUrl = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=" . urlencode($query) . "&sort_by=popularity.desc";

    // Initialize cURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL and get the response
    $response = curl_exec($curl);

    // Check if cURL execution was successful
    if (curl_errno($curl)) {
        error_log('cURL error: ' . curl_error($curl));
        curl_close($curl);
        return ['error' => 'API request failed'];
    }

    curl_close($curl);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Handle cases where the response is not valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON error: ' . json_last_error_msg());
        error_log('API response: ' . $response); // Log the response to check for HTML errors
        return ['error' => 'Invalid JSON response'];
    }

    // Extract unique movie titles from the response
    $suggestions = [];
    $uniqueTitles = []; // Array to track unique titles
    if (isset($data['results'])) {
        foreach ($data['results'] as $movie) {
            $title = $movie['title'];
            if (!in_array($title, $uniqueTitles)) { // Check for uniqueness
                $uniqueTitles[] = $title; // Add to unique titles
                $suggestions[] = [
                    'title' => $title,
                    'release_date' => $movie['release_date'] ?? 'N/A', // Include release date if available
                    'popularity' => $movie['popularity'] ?? 0 // Include popularity if available
                ];
            }
        }
    }

    return $suggestions;
}

function getActorIdByName($actorName)
{
    $apiKey = apiKey; // Replace with your TMDB API key
    $actorName = urlencode($actorName); // Encode the actor's name for the URL
    $url = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$actorName}";

    // Make the API request
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if results were found
    if (!empty($data['results'])) {
        // Return the first result's ID
        return $data['results'][0]['id']; // Return the TMDB actor ID
    } else {
        return null; // No actor found
    }
}

function getActorSuggestions($query)
{
    // Replace with your actual TMDB API key
    $apiKey = apiKey;
    $url = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query=" . urlencode($query);

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return []; // Return an empty array if the request fails
    }

    $data = json_decode($response, true);
    if (isset($data['results']) && !empty($data['results'])) {
        return $data['results'];
    }

    return []; // Return an empty array if there are no results
}
function getMovieGenres()
{
    $apiKey = apiKey; // Replace with your TMDB API key
    $url = "https://api.themoviedb.org/3/genre/movie/list?api_key={$apiKey}&language=en-US";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return []; // Return an empty array in case of an error
    }

    $data = json_decode($response, true);
    return $data['genres'] ?? []; // Return the genres or an empty array if not found
}
