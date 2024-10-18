<?php
if (isset($_GET['actorId'])) {
    $actorId = htmlspecialchars(trim($_GET['actorId']));
    $actorDetails = getActorFullDetails($actorId); // Pull actor details using the specified method
    // var_dump($actorDetails);
} else {
    echo "No ID specified.";
    exit; // Exit if no actor ID is provided
}

// Initialize values
$actorInfo = [];
$movieCredits = [];

if (is_array($actorDetails)) {
    $actorInfo = $actorDetails['actorInfo'];
    $movieCredits = $actorDetails['movieCredits'];
} else {
    echo "Actor details not found.";
    exit; // Exit if actor details are not found
}
?>

<body class="actordetailbody">

    <main class="actor-details">
        <div class="actor-info">
            <div class="poster">
                <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($actorInfo['profile_path']); ?>" alt="<?php echo htmlspecialchars($actorInfo['name']); ?>" class="actor-profile-img">
            </div>
            <div class="actor-details-text">
                <h1 id="actor-name"><strong><?php echo htmlspecialchars($actorInfo['name']); ?></strong></h1>
                <p id="movie-info-release_date"> <?php echo $actorInfo['birthday']; ?></p>
                <p id="actor-bio"><?php echo htmlspecialchars($actorInfo['biography'] ?? 'N/A'); ?></p>
            </div>
            <div class="actor-additional-info">
                <h2 class="highlight">Additional Information</h2>
                <p><strong>Age:</strong> <?php echo isset($actorInfo['birthday']) ? (new DateTime())->diff(new DateTime($actorInfo['birthday']))->y .' Years old': 'N/A';?></p>
                <p><strong>Place of Birth:</strong> <?php echo htmlspecialchars($actorInfo['place_of_birth'] ?? 'N/A'); ?></p>
                <p><strong>Popularity:</strong> <?php echo htmlspecialchars($actorInfo['popularity'] ?? 'N/A'); ?></p>
            </div>
        </div>
        <div class="filmography">

            <h2>Filmography</h2>
            <ul>
                <?php if (!empty($movieCredits)) {
                    // var_dump($movieCredits);
                    searchDisplay($movieCredits);
                } else {
                ?>
                    <li>No movie credits available.</li>
                <?php
                }
                ?>
            </ul>
        </div>

    </main>

