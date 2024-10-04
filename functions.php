<?php
require_once 'display.php'; // Include the functions file
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
        searchDisplay($data);
        // displayScrollableMoviesTMDb($data);
    } else {
        echo "Movie not found.\n";
    }
}

function getMoviesByGenre($genreId, $page = 1)
{
    $apiKey = apiKey;
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&language=en-US&sort_by=popularity.desc&with_genres={$genreId}&page={$page}";

    // Use file_get_contents to fetch the data
    $response = file_get_contents($url);

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    if ($response === FALSE) {
        die('Error occurred while fetching movies by genre.');
    }else {
        scrollableMoviesTMDbDisplay($data);
    }
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


?>
