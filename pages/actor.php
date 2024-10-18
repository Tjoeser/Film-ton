<?php
$actorId = 1190668; // Replace with the actual actor ID
$actorDetails = getActorFullDetails($actorId);
if ($live) {
    echo "livce is true";
}

if (is_array($actorDetails)) {
    $actorInfo = $actorDetails['actorInfo'];
    $movieCredits = $actorDetails['movieCredits'];

    // Display Actor Information
    echo "<h1>" . $actorInfo['name'] . "</h1>";
    echo "<p><strong>Biography:</strong> " . $actorInfo['biography'] . "</p>";
    echo "<p><strong>Birthday:</strong> " . $actorInfo['birthday'] . "</p>";
    echo "<p><strong>Place of Birth:</strong> " . $actorInfo['place_of_birth'] . "</p>";
    echo "<p><strong>Popularity:</strong> " . $actorInfo['popularity'] . "</p>";
    echo "<p><strong>Known For:</strong> " . $actorInfo['known_for_department'] . "</p>";
    echo "<p><strong>Profile Picture:</strong><br> <img src='https://image.tmdb.org/t/p/w500" . $actorInfo['profile_path'] . "' alt='" . $actorInfo['name'] . "'></p>";

    // Display Movie Credits
    echo "<h2>Movies:</h2>";
    echo "<ul>";
    foreach ($movieCredits['cast'] as $movie) {
        echo "<li><strong>" . $movie['title'] . "</strong> (" . $movie['release_date'] . ") - " . $movie['character'] . "</li>";
    }
    echo "</ul>";
} else {
    echo $actorDetails; // Display error message
}
