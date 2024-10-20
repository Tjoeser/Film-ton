
// Function to get the value of a cookie by name
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// This function checks if the user is logged in
function checkLoginStatus() {
    const loggedIn = getCookie('loggedin');

    const loginButton = document.querySelector('button[name="page"][value="account"]');

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
let currentPage = 1; // Track the current page

// Function to load movies from TMDB API
function loadMovies(page) {
    const apiKey = "<?php echo apiKey; ?>"; // Embed PHP constant in JavaScript
    const url = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&page=${page}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const resultsContainer = document.getElementById('results'); // Adjust to your actual results container ID
            data.results.forEach(movie => {
                const movieElement = document.createElement('div');
                movieElement.innerHTML = `<h3>${movie.title}</h3><p>${movie.overview}</p>`; // Adjust according to your structure
                resultsContainer.appendChild(movieElement);
            });
            // Hide the Load More button if there are no more movies
            if (data.page >= data.total_pages) {
                document.getElementById('loadMoreButton').style.display = 'none';
            }
        })
        .catch(error => console.error('Error loading movies:', error));
}

// Function to handle Load More button click
function loadMoreMovies() {
    currentPage += 1; // Increment the page number
    loadMovies(currentPage); // Load movies for the new page
}

// Load initial movies when the page loads
document.addEventListener('DOMContentLoaded', () => {
    loadMovies(currentPage);
});

// Event listener for Load More button
document.getElementById('loadMoreButton').addEventListener('click', loadMoreMovies);
