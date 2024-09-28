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
