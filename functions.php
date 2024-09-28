<?php
require_once 'display.php'; // Include the functions file

function search(string $search): string
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://moviedatabase8.p.rapidapi.com/FindByTitle/" . urlencode($search),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: moviedatabase8.p.rapidapi.com",
            "x-rapidapi-key: 1133101975msh7999bcdaf673e85p1026d1jsndfc0a51f4af2"
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } elseif ($httpCode !== 200) {
        return "HTTP Error Code: " . $httpCode;
    } else {
        return $response; // Return the response for display
    }
}

function getMovieDetailsByImdbId(string $imdbId): string
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://moviedatabase8.p.rapidapi.com/FindByImbdId/" . urlencode($imdbId),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: moviedatabase8.p.rapidapi.com",
            "x-rapidapi-key: 1133101975msh7999bcdaf673e85p1026d1jsndfc0a51f4af2"
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } elseif ($httpCode !== 200) {
        return "HTTP Error Code: " . $httpCode;
    } else {
        return $response; // Return the response for display
    }
}

function filterMovies($filters = [])
{
    $defaultFilters = [
        'MinRating' => '',
        'MaxRating' => '',
        'MinYear' => '',
        'MaxYear' => '',
        'MinRevenue' => '',
        'MaxRevenue' => '',
        'Genre' => 'Action',
        'MinRuntime' => '',
        'MaxRuntime' => '',
        'OriginalLanguage' => '',
        'SpokenLanguage' => '',
        'Limit' => '30'
    ];

    // Merge default filters with user-provided filters
    $filters = array_merge($defaultFilters, $filters);

    $query = http_build_query($filters);
    $url = "https://moviedatabase8.p.rapidapi.com/Filter?" . $query;

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: moviedatabase8.p.rapidapi.com",
            "x-rapidapi-key: 1133101975msh7999bcdaf673e85p1026d1jsndfc0a51f4af2"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $data = json_decode($response, true);
        scrollMovieDisplay($data);
    }
}

function movieRandomizer($limit)
{

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://moviedatabase8.p.rapidapi.com/Random/" . $limit,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: moviedatabase8.p.rapidapi.com",
            "x-rapidapi-key: 1133101975msh7999bcdaf673e85p1026d1jsndfc0a51f4af2"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $data = json_decode($response, true);
        scrollMovieDisplay($data);
    }
}


function getCountry()
{
    $countrycode = "NL"; // Define the country code (NL for Netherlands)
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://streaming-availability.p.rapidapi.com/countries/" . $countrycode . "?output_language=en",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: streaming-availability.p.rapidapi.com",
            "x-rapidapi-key: 1133101975msh7999bcdaf673e85p1026d1jsndfc0a51f4af2"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // Decode the JSON response into an associative array
        $data = json_decode($response, true);

        // Check if data is correctly parsed
        if ($data && is_array($data)) {
            // Start HTML rendering
            echo '<div style="font-family: Arial, sans-serif; padding: 20px;">';
            echo '<h1>Streaming Services in ' . htmlspecialchars($data['name']) . ' (' . htmlspecialchars(strtoupper($data['countryCode'])) . ')</h1>';

            // Loop through services
            foreach ($data['services'] as $service) {
                echo '<div style="border: 1px solid #ccc; border-radius: 8px; padding: 15px; margin-bottom: 20px; width: 300px;">';
                echo '<img src="' . htmlspecialchars($service['imageSet']['lightThemeImage']) . '" alt="' . htmlspecialchars($service['name']) . ' Logo" style="width: 100px; height: auto;">';
                echo '<h2 style="color: ' . htmlspecialchars($service['themeColorCode']) . ';">' . htmlspecialchars($service['name']) . '</h2>';
                echo '<p><strong>Website:</strong> <a href="' . htmlspecialchars($service['homePage']) . '" target="_blank">' . htmlspecialchars($service['homePage']) . '</a></p>';

                // Streaming options
                echo '<h3>Streaming Options</h3>';
                echo '<ul>';
                foreach ($service['streamingOptionTypes'] as $option => $available) {
                    $status = $available ? 'Available' : 'Not Available';
                    echo '<li>' . ucfirst($option) . ': ' . $status . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<p>Error processing the JSON data.</p>';
        }
    }
}

