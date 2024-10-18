<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../functions.php'; // Include the functions file where GetMovieSuggestions is defined

if (isset($_GET['query'])) {
    $query = htmlspecialchars(trim($_GET['query']));
    $suggestions = GetMovieSuggestions($query);
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
}
?>
