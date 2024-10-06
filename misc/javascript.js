// Ensure the DOM is fully loaded before running the script
document.addEventListener('DOMContentLoaded', (event) => {
    // Get the button element
    const randomizerButton = document.getElementById('randomizer-btn');

    // Check if the button exists to avoid errors
    if (randomizerButton) {
        // Add an event listener for the button click
        randomizerButton.addEventListener('click', () => {
            // Call the movieRandomizer function
            console.log("yasas")
            movieRandomizer("1");
        });
    }
});




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
checkLoginStatus();

// Call the checkLoginStatus function on page load
window.onload = checkLoginStatus;


// Check if the button with ID 'randomizer-btn' exists on the page
const randomizerButton = document.getElementById('randomizer-btn');

if (randomizerButton) {
    randomizerButton.addEventListener('click', function () {
        // Make an AJAX request to call the randomizer
        fetch('randomizer.php') // Calls the randomizer.php file to trigger the randomizer function
            .then(response => response.text())
            .then(data => {
                document.getElementById('random-movie-result').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
}
