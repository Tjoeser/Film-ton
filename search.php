<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = htmlspecialchars(trim($_POST['search']));
    $response = search($search);
    $data = json_decode($response, true);
}
?>

<body>
    <main>
        <form id="search-form" method="post">
            <input type="text" name="search" placeholder="Movie title" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['search'])) {
            if (is_array($data) && !empty($data)) {
                echo "<h2>Title: " . htmlspecialchars($search) . "</h2>";
                searchDisplay($data);
            } else {
                echo "<p>No results found. Try another search or use the randomizer below.</p>";
            }
        }
        ?>

        <button id="randomizer-btn">Or try the randomizer</button>
        <div id="random-movie-result"></div>
    </main>

    <script>
        document.getElementById('randomizer-btn').addEventListener('click', function() {
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
    </script>
</body>
</html>
