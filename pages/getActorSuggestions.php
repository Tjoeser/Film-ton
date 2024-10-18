<?php
include '../functions.php'; // Adjust the path as necessary

if (isset($_GET['query'])) {
    $query = htmlspecialchars(trim($_GET['query']));
    $suggestions = getActorSuggestions($query);
    echo json_encode($suggestions);
} else {
    echo json_encode([]); // Return an empty array if no query is provided
}
?>
