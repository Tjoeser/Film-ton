<body>
    <main id="search-page">
        <form id="movie-search-form" method="post">
            <input type="text" name="search" id="movie-title-input" placeholder="Movie title" autocomplete="off">
            <input type="text" name="actor" id="actor-input" placeholder="Actor name" autocomplete="off">
            <div id="actor-suggestions-box" style="display: none;"></div> <!-- Container for actor suggestions -->
            <input type="text" name="year" id="year-input" placeholder="Year" pattern="\d{4}" title="Please enter a valid year (4 digits)" maxlength="4" autocomplete="off">

            <select name="genre" id="genre-dropdown">
                <option value="">Select Genre</option>
                <?php
                // Fetch genres
                $genres = getMovieGenres();
                foreach ($genres as $genre) {
                    echo "<option value=\"{$genre['id']}\">{$genre['name']}</option>";
                }
                ?>
            </select>
            <button type="submit" id="search-submit-btn">Search</button>
        </form>

        <div id="suggestions-box" style="display: none;"></div> <!-- Container for suggestions -->

        <?php
        $year = htmlspecialchars(trim($_POST['year'] ?? ''));
        $genre = htmlspecialchars(trim($_POST['genre'] ?? ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the actor input is set
            if (isset($_POST['actor'])) {
                $actor = htmlspecialchars(trim($_POST['actor']));
                if ($actor) {
                    $id = getActorIdByName($actor); // Get the actor ID
                    if ($id) {
                        // Redirect to the actor page using header
                        header("Location: ?page=actor&actorId=" . urlencode($id));
                        exit; // Make sure to exit after redirecting
                    } else {
                        echo "Actor not found.";
                    }
                }
            }

            // Check if the movie search input is set
            if (isset($_POST['search'])) {
                GetMoviesByTitle(trim($_POST['search']));
            }
            if (isset($_POST['genre'])) {
                getMoviesByGenre($_POST['genre'],false);
            }
        }

        ?>

        <form id="randomizer-form" method="post">
            <input type="hidden" name="action" value="randomize">
            <button type="submit" id="randomizer-submit-btn">Or try the randomizer</button>
        </form>

        <div id="random-movie-display"><?php getRandomMovie(); ?></div>
    </main>

    <script>
        // Populate genres from TMDB API
        fetch('https://api.themoviedb.org/3/genre/movie/list?api_key=YOUR_API_KEY&language=en-US')
            .then(response => response.json())
            .then(data => {
                const genreDropdown = document.getElementById('genre-dropdown');
                data.genres.forEach(genre => {
                    const option = document.createElement('option');
                    option.value = genre.id; // Assuming you're using genre ID
                    option.textContent = genre.name;
                    genreDropdown.appendChild(option);
                });
            });

        let selectedIndex = -1; // To track the currently selected movie suggestion

        document.getElementById('movie-title-input').addEventListener('input', function() {
            let query = this.value;
            if (query.length >= 2) { // Start showing suggestions after 2 characters
                fetchMovieSuggestions(query);
            } else {
                document.getElementById('suggestions-box').style.display = 'none';
                selectedIndex = -1; // Reset selected index
            }
        });

        function fetchMovieSuggestions(query) {
            fetch('pages/suggestions.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    let suggestionsBox = document.getElementById('suggestions-box');
                    if (data.length > 0) {
                        let suggestionsHTML = data.map((item, index) =>
                            `<div class="suggestion-item" 
                            onclick="fillMovieSearchBar('${item.title}')" 
                            onmouseover="highlightMovieSuggestion(${index})">${item.title}</div>`
                        ).join('');
                        suggestionsBox.innerHTML = suggestionsHTML;
                        suggestionsBox.style.display = 'block';
                        selectedIndex = -1; // Reset the index when new suggestions are fetched
                    } else {
                        suggestionsBox.style.display = 'none';
                        selectedIndex = -1; // Reset selected index
                    }
                });
        }

        function fillMovieSearchBar(movieTitle) {
            const searchInput = document.getElementById('movie-title-input');
            searchInput.value = movieTitle; // Fill the input with the selected movie title
            document.getElementById('suggestions-box').style.display = 'none'; // Hide suggestions after selection
        }

        // Function to highlight a movie suggestion
        function highlightMovieSuggestion(index) {
            const suggestions = document.querySelectorAll('.suggestion-item');
            suggestions.forEach((suggestion, idx) => {
                suggestion.classList.toggle('highlighted', idx === index);
            });
        }

        document.getElementById('movie-title-input').addEventListener('keydown', function(event) {
            const suggestions = document.querySelectorAll('.suggestion-item');
            if (event.key === 'ArrowDown') {
                selectedIndex = (selectedIndex + 1) % suggestions.length; // Move down
                highlightMovieSuggestion(selectedIndex);
                event.preventDefault(); // Prevent the cursor from moving in the input
            } else if (event.key === 'ArrowUp') {
                selectedIndex = (selectedIndex - 1 + suggestions.length) % suggestions.length; // Move up
                highlightMovieSuggestion(selectedIndex);
                event.preventDefault(); // Prevent the cursor from moving in the input
            } else if (event.key === 'Enter') {
                if (selectedIndex >= 0) {
                    const selectedSuggestion = suggestions[selectedIndex].textContent;
                    fillMovieSearchBar(selectedSuggestion); // Fill input with the selected suggestion
                }
            }
        });

        let actorSelectedIndex = -1; // To track the currently selected actor suggestion

        document.getElementById('actor-input').addEventListener('input', function() {
            let query = this.value;
            if (query.length >= 2) { // Start showing suggestions after 2 characters
                fetchActorSuggestions(query);
            } else {
                document.getElementById('actor-suggestions-box').style.display = 'none';
                actorSelectedIndex = -1; // Reset selected index
            }
        });

        function fetchActorSuggestions(query) {
            fetch('pages/getActorSuggestions.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    const uniqueSuggestions = [...new Set(data.map(actor => actor.name))]; // Remove duplicates
                    let suggestionsBox = document.getElementById('actor-suggestions-box');
                    if (uniqueSuggestions.length > 0) {
                        let suggestionsHTML = uniqueSuggestions.map((actor, index) =>
                            `<div class="suggestion-item" 
                            onclick="fillActorSearchBar('${actor}', ${data[index].id})" 
                            onmouseover="highlightActorSuggestion(${index})">${actor}</div>`
                        ).join('');
                        suggestionsBox.innerHTML = suggestionsHTML;
                        suggestionsBox.style.display = 'block';
                        actorSelectedIndex = -1; // Reset the index when new suggestions are fetched
                    } else {
                        suggestionsBox.style.display = 'none';
                        actorSelectedIndex = -1; // Reset selected index
                    }
                });
        }

        function fillActorSearchBar(actorName, actorId) {
            const actorInput = document.getElementById('actor-input');
            actorInput.value = actorName; // Fill the input with the selected actor name
            document.getElementById('actor-suggestions-box').style.display = 'none'; // Hide suggestions after selection
            // Store the actor ID in a hidden input or variable if needed
            // document.getElementById('actor-id-input').value = actorId; // If you want to use it later
        }

        // Function to highlight an actor suggestion
        function highlightActorSuggestion(index) {
            const suggestions = document.querySelectorAll('#actor-suggestions-box .suggestion-item');
            suggestions.forEach((suggestion, idx) => {
                suggestion.classList.toggle('highlighted', idx === index);
            });
        }

        document.getElementById('actor-input').addEventListener('keydown', function(event) {
            const suggestions = document.querySelectorAll('#actor-suggestions-box .suggestion-item');
            if (event.key === 'ArrowDown') {
                actorSelectedIndex = (actorSelectedIndex + 1) % suggestions.length; // Move down
                highlightActorSuggestion(actorSelectedIndex);
                event.preventDefault(); // Prevent the cursor from moving in the input
            } else if (event.key === 'ArrowUp') {
                actorSelectedIndex = (actorSelectedIndex - 1 + suggestions.length) % suggestions.length; // Move up
                highlightActorSuggestion(actorSelectedIndex);
                event.preventDefault(); // Prevent the cursor from moving in the input
            } else if (event.key === 'Enter') {
                if (actorSelectedIndex >= 0) {
                    const selectedSuggestion = suggestions[actorSelectedIndex].textContent;
                    fillActorSearchBar(selectedSuggestion); // Fill input with the selected suggestion
                }
            }
        });
    </script>

    <style>
        .highlighted {
            background-color: #e0e0e0;
            /* Change this to your preferred highlight color */
        }
    </style>
</body>