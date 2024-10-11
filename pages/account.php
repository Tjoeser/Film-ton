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
            // Determine if it's a single value or an array
            $watchProviders = $_POST['watch_providers'];
            if (!is_array($watchProviders)) {
                $watchProviders = [$watchProviders]; // Wrap single value in an array
            }
            addStreamingServicesDatahandler($watchProviders); // Pass the array to the function
        } else {
            echo "No watch providers selected.";
        }
    }
}

// Section 2: Updated functionality from Snippet 2
$services = pullSpecificAccountDataDatahandler('streaming');
$result = getWatchProvidersInCountry(); // Assume this function returns an array of provider names
?>

<body>
    <main>
        <h1>Account Settings</h1>
        <form method="POST" action="">
            <button type="submit" name="logout">Logout</button>
            <button type="submit" name="delete">Delete Account</button>
        </form>

        <form method="POST" action="">
            <fieldset>
                <legend>Search Your Watch Providers:</legend>
                <input type="text" id="searchInput" placeholder="Type to search..." onkeyup="filterProviders()" autocomplete="off">
                <div id="resultList" style="display: none;"></div>

                <!-- Submit button to send selected values -->
                <button type="submit" name="serviceselect" id="submitBtn" style="display: none;">Submit</button>

                <div id="selectedProviders" style="margin-top: 10px;"></div> <!-- Display selected providers here -->
                <input type="hidden" name="watch_providers" id="hiddenWatchProviders" value=""> <!-- Hidden input to store selected providers -->
            </fieldset>
        </form>

        <script>
            const providers = <?php echo json_encode($result); ?>; // Pass PHP array to JavaScript
            const services = <?php echo json_encode($services); ?>; // Pass services array to JavaScript
            const resultList = document.getElementById('resultList');
            const submitBtn = document.getElementById('submitBtn');
            const selectedProvidersDiv = document.getElementById('selectedProviders');
            const hiddenWatchProviders = document.getElementById('hiddenWatchProviders'); // Hidden input
            let selectedProviders = []; // Initialize selectedProviders with the values from $services

            // Update display for selected providers on load
            updateSelectedProvidersDisplay(); 

            function filterProviders() {
                const input = document.getElementById('searchInput').value.toLowerCase();
                resultList.innerHTML = ''; // Clear previous results
                let hasResults = false; // Flag to check if there are results

                if (input) {
                    providers.forEach(provider => {
                        if (provider.toLowerCase().includes(input)) {
                            hasResults = true;
                            const isChecked = selectedProviders.includes(provider); // Check if the provider is already selected
                            const div = document.createElement('div');
                            div.innerHTML = `<input type="checkbox" id="${provider}" name="watch_providers[]" value="${provider}" ${isChecked ? 'checked' : ''} onclick="toggleProvider('${provider}')"> 
                                    <label for="${provider}">${provider}</label>`;
                            resultList.appendChild(div);
                        }
                    });
                    resultList.style.display = hasResults ? 'block' : 'none'; // Show or hide the results list
                    submitBtn.style.display = hasResults || selectedProviders.length > 0 ? 'inline' : 'none'; // Show submit button if there are results or selected providers
                } else {
                    resultList.style.display = 'none'; // Hide results if input is empty
                    submitBtn.style.display = selectedProviders.length > 0 ? 'inline' : 'none'; // Show submit button if there are selected providers
                }
                updateSelectedProvidersDisplay(); // Update selected providers display
            }

            function toggleProvider(provider) {
                const index = selectedProviders.indexOf(provider);
                if (index > -1) {
                    // If already selected, remove from the array
                    selectedProviders.splice(index, 1);
                } else {
                    // If not selected, add to the array
                    selectedProviders.push(provider);
                }
                updateSelectedProvidersDisplay(); // Update the selected providers display
            }

            function updateSelectedProvidersDisplay() {
                selectedProvidersDiv.innerHTML = ''; // Clear previous selections
                selectedProviders.forEach(provider => {
                    const div = document.createElement('div');
                    div.style.display = 'inline-block';
                    div.style.marginRight = '5px';
                    div.style.padding = '2px 5px';
                    div.style.border = '1px solid #ccc';
                    div.style.borderRadius = '3px';
                    div.style.backgroundColor = '#e0e0e0'; // Light background for visibility
                    div.innerHTML = `${provider} <span style="cursor:pointer; color:red;" onclick="removeProvider('${provider}')">X</span>`;
                    selectedProvidersDiv.appendChild(div);
                });

                // Update hidden input value to submit selected providers
                hiddenWatchProviders.value = selectedProviders.join(','); // Join selected providers for submission

                // Show or hide the submit button based on the selected providers
                submitBtn.style.display = selectedProviders.length > 0 ? 'inline' : 'none';
            }

            function removeProvider(provider) {
                const index = selectedProviders.indexOf(provider);
                if (index > -1) {
                    selectedProviders.splice(index, 1); // Remove provider from the array
                }
                updateSelectedProvidersDisplay(); // Update the selected providers display
            }
        </script>

        <div>Your watchlist:<br>
            <?php
            pullWatchlist();
            ?>
        </div>
    </main>
</body>
