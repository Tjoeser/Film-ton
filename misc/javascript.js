


// Function to get the value of a cookie by name
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// This function checks if the user is logged in
function checkLoginStatus() {
    const loggedIn = getCookie('loggedin');

    const loginButton = document.querySelector('button[name="page"][value="login"]');

    if (loggedIn === 'true') {
        console.log("User is logged in.");
        // Change the Login button text and value
        if (loginButton) {
            loginButton.textContent = "Account"; // Change the text
            loginButton.value = "account"; // Change the value
        }
    } else {
        console.log("User is not logged in.");
        // Optionally, you can reset the button to its original state if needed
        if (loginButton) {
            loginButton.textContent = "Login"; // Reset to original text
            loginButton.value = "login"; // Reset to original value
        }
    }
}

// Call the function to check login status when the page loads

// Call the checkLoginStatus function on page load
