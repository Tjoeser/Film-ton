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


// // Function to change the URL based on the page identifier
// function updateURLBasedOnPage() {
//     // Get the page identifier element
//     const pageIdentifier = document.getElementById('page-identifier');
//     if (pageIdentifier) {
//         // Get the data-page attribute
//         const currentPage = pageIdentifier.getAttribute('data-page');

//         console.log(currentPage);

//         // Construct the new URL based on the current page
//         let newURL = `Film-ton/${currentPage}`; // Adjust as needed for your URL structure

//         // Update the URL in the address bar without reloading the page
//         const state = {}; // Optional: to store state data
//         history.pushState(state, '', newURL); // Empty string for the title, set to the new URL
//     }
// }

// // Call the function on page load
// document.addEventListener('DOMContentLoaded', updateURLBasedOnPage);


document.getElementById('randomizer-btn').addEventListener('click', function () {
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