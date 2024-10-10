<?php
// Include the functions file if necessary
// include './functions.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check which button was pressed
    if (isset($_POST['logout'])) {
        logoutAccount(); // Call the logout function
    } elseif (isset($_POST['delete'])) {
        deleteAccount(); // Call the delete account function
    } elseif (isset($_POST['serviceselect'])) {
        // Check if any watch providers were selected
        if (isset($_POST['watch_providers'])) {
            // var_dump the selected watch providers
            var_dump($_POST['watch_providers']);
        } else {
            echo "No watch providers selected.";
        }
    }
}
?>

<body>
    <main>
        <h1>Account Settings</h1>
        <form method="POST" action="">
            <button type="submit" name="logout">Logout</button>
            <button type="submit" name="delete">Delete Account</button>
        </form>

        <?php
        $result = getWatchProvidersInCountry(); // Assume this function returns an array of provider names
        ?>

        <form method="POST" action="">
            <fieldset>
                <legend>Select Your Watch Providers:</legend>
                <?php if (!empty($result)): ?>
                    <?php foreach ($result as $provider): ?>
                        <div>
                            <input type="checkbox" id="<?php echo htmlspecialchars($provider); ?>" name="watch_providers[]" value="<?php echo htmlspecialchars($provider); ?>">
                            <label for="<?php echo htmlspecialchars($provider); ?>"><?php echo htmlspecialchars($provider); ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No providers available.</p>
                <?php endif; ?>
            </fieldset>

            <button type="submit" name="serviceselect">Submit</button>
        </form>

        <div>Your watchlist:<br>
        <?php
        pullWatchlist();
        ?>
        </div>
    </main>
</body>
