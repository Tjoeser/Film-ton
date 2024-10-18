<?php
// Include the functions file if necessary
// include './functions.php'; 

if (empty($_COOKIE['loggedin'])) {
    header("Location: ?page=login");
    exit; // It's good practice to call exit after a redirect to prevent further code execution
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['logout'])) {
        logoutAccount();
    } elseif (isset($_POST['delete'])) {
        deleteAccount();
    } elseif (isset($_POST['serviceselect'])) {
        if (isset($_POST['watch_providers'])) {
            $watchProviders = $_POST['watch_providers'];
            if (!is_array($watchProviders)) {
                $watchProviders = [$watchProviders];
            }
            addStreamingServicesDatahandler($watchProviders);
        } else {
            echo "No watch providers selected.";
        }
    }
}

// Fetch watch providers from the function
$services = getWatchProvidersInCountry();

// Ensure $services is cleaned and converted into an array if necessary
if (is_string($services)) {
    $services = json_decode($services, true);
}

// Clean the fetched services
if (is_array($services)) {
    $cleanedServices = array_map(function ($service) {
        return htmlspecialchars(trim($service));
    }, $services);
} else {
    $cleanedServices = [];
}
// Fetch specific account data
$accountData = pullSpecificAccountDataDatahandler('streaming');

// Ensure $accountData is cleaned and converted into an array if necessary
if (is_string($accountData)) {
    $accountData = json_decode($accountData, true);
}

// If $accountData is an array, use it to populate selectedProviders
$selectedProviders = [];
if (is_array($accountData)) {
    $selectedProviders = array_map(function ($service) {
        return htmlspecialchars(trim($service));
    }, $accountData);
}

// Ensure each provider is split if they are provided as a comma-separated string
if (!empty($selectedProviders)) {
    $selectedProviders = explode(',', implode(',', $selectedProviders));
}

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
                <div id="selectedProviders"></div> <!-- Display selected providers here -->
                <input type="text" id="searchInput" placeholder="Type to search..." onkeyup="filterProviders()" autocomplete="off">
                <div id="resultList" style="display: none;"></div>
                <button type="submit" name="serviceselect" id="submitBtn" style="display: none;">Submit</button>

                <input type="hidden" name="watch_providers" id="hiddenWatchProviders" value=""> <!-- Hidden input to store selected providers -->
            </fieldset>
        </form>

        <div>Your watchlist:<br>
            <?php pullWatchlist(); ?>
        </div>
    </main>
</body>

<script>
    // Handle the case where $cleanedServices might be null or undefined
    const providers = <?php echo !empty($cleanedServices) ? json_encode($cleanedServices) : '[]'; ?>;
    const selectedProviders = <?php echo !empty($selectedProviders) ? json_encode($selectedProviders) : '[]'; ?>;

    const resultList = document.getElementById('resultList');
    const submitBtn = document.getElementById('submitBtn');
    const selectedProvidersDiv = document.getElementById('selectedProviders');
    const hiddenWatchProviders = document.getElementById('hiddenWatchProviders');

    // Initialize selectedProviders with the cleaned services from the account data
    let selectedProvidersArray = selectedProviders;

    // Update display for selected providers on load
    updateSelectedProvidersDisplay();

    function filterProviders() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        resultList.innerHTML = ''; // Clear previous results
        let hasResults = false;

        if (input) {
            providers.forEach(provider => {
                if (provider.toLowerCase().includes(input)) {
                    hasResults = true;
                    const isChecked = selectedProvidersArray.includes(provider);
                    const div = document.createElement('div');
                    div.innerHTML = `<input type="checkbox" id="${provider}" name="watch_providers[]" value="${provider}" ${isChecked ? 'checked' : ''} onclick="toggleProvider('${provider}')"> 
                                <label for="${provider}">${provider}</label>`;
                    resultList.appendChild(div);
                    console.log('Provider added:', provider); // Log added provider
                }
            });
            resultList.style.display = hasResults ? 'block' : 'none';
            submitBtn.style.display = hasResults || selectedProvidersArray.length > 0 ? 'inline' : 'none';
        } else {
            resultList.style.display = 'none';
            submitBtn.style.display = selectedProvidersArray.length > 0 ? 'inline' : 'none';
        }
        updateSelectedProvidersDisplay();
    }


    function toggleProvider(provider) {
        const index = selectedProvidersArray.indexOf(provider);
        if (index > -1) {
            selectedProvidersArray.splice(index, 1);
        } else {
            selectedProvidersArray.push(provider);
        }
        updateSelectedProvidersDisplay();
    }

    function updateSelectedProvidersDisplay() {
        // Clear the previous content
        selectedProvidersDiv.innerHTML = '';

        console.log(selectedProvidersArray);
        // Loop through each selected provider and create a div for it
        selectedProvidersArray.forEach(provider => {
            const div = document.createElement('div');
            div.style.display = 'inline-block';
            div.style.marginRight = '5px';
            div.style.padding = '2px 5px';
            div.style.border = '1px solid #ccc';
            div.style.borderRadius = '3px';
            div.style.backgroundColor = '#e0e0e0';

            // Create a span for the provider
            const providerSpan = document.createElement('span');
            providerSpan.textContent = provider;

            // Create a remove button as a span
            const removeSpan = document.createElement('span');
            removeSpan.style.cursor = 'pointer';
            removeSpan.style.color = 'red';
            removeSpan.textContent = ' X';
            removeSpan.onclick = () => removeProvider(provider); // Using a closure to bind the current provider

            // Append the provider span and the remove button to the div
            div.appendChild(providerSpan);
            div.appendChild(removeSpan);

            // Append the new div to the selectedProvidersDiv
            selectedProvidersDiv.appendChild(div);
        });

        // Update hidden input value for form submission
        hiddenWatchProviders.value = selectedProvidersArray.join(','); // Ensure it's comma-separated

        // Toggle the submit button based on the selected providers count
        submitBtn.style.display = selectedProvidersArray.length > 0 ? 'inline' : 'none';
    }


    function removeProvider(provider) {
        const index = selectedProvidersArray.indexOf(provider);
        if (index > -1) {
            selectedProvidersArray.splice(index, 1);
        }
        updateSelectedProvidersDisplay();
    }
</script>