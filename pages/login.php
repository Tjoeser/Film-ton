<?php
$email = '';
$password = '';
$country = '';

// Check if the user is logged in
if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] === 'true') {
    echo "<h1>Welcome!</h1>";
    header("Location: ?page=account"); // Redirect to the specific page
} else {
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';

        if (isset($_POST['register'])) {
            registerAccount($email, $password, $country);
        } elseif (isset($_POST['login'])) {
            loginAccount($email, $password);
        }
    }

?>

    <body>
        <main>
            <button id="toggle-form" onclick="toggleForm()">Switch to Register</button>

            <div id="form-container">
                <?php loginForm($email, $password); ?>
            </div>
        </main>

        <script>
            let isLoginFormVisible = false; // Flag to track the visible form

            function toggleForm() {
                const formContainer = document.getElementById('form-container');
                const button = document.getElementById('toggle-form');

                if (isLoginFormVisible) {
                    // If the registration form is visible, show the login form
                    formContainer.innerHTML = `<?php loginForm($email, $password); ?>`;
                    button.innerText = 'Switch to Register';
                } else {
                    // If the login form is visible, show the registration form
                    formContainer.innerHTML = `<?php registrationForm(); ?>`;
                    button.innerText = 'Switch to Login';
                }

                isLoginFormVisible = !isLoginFormVisible; // Toggle the flag
            }
        </script>
    </body>
<?php
}
?>