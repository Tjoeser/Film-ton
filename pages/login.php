<?php
session_start();

// Process the form submission if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Debug: Check sanitized values
    echo "Username: $username<br>";
    echo "Password: $password<br>";

    // Save data if values are not empty
    if (!empty($username) && !empty($password)) {
        $data = "[$username][$password]\n";
        $filePath = 'misc/login_db.txt'; // Ensure this path is correct

        // Attempt to save to the file
        if (file_put_contents($filePath, $data, FILE_APPEND | LOCK_EX) === false) {
            echo "Error saving data. Please check file permissions.";
        } else {
            echo "Registration successful!";
        }
    } else {
        echo "Username and password cannot be empty.";
    }
}
?>

<body>
    <div id="page-identifier" data-page="login">
        <h2>Register</h2>
        <form id="registration-form" method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" value="Register">
        </form>
    </div>
</body>
