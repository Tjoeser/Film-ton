<?php
// Include the functions file if necessary
// include './functions.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check which button was pressed
    if (isset($_POST['logout'])) {
        logoutAccount(); // Call the logout function
    } elseif (isset($_POST['delete'])) {
        deleteAccount(); // Call the delete account function
    }
}
?>

<body>
    <h1>Account Settings</h1>
    <form method="POST" action="">
        <button type="submit" name="logout">Logout</button>
        <button type="submit" name="delete">Delete Account</button>
    </form>
</body>
