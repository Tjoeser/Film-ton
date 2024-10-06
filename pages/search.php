<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = htmlspecialchars(trim($_POST['search']));
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
                GetMoviesByTitle($search);
        } else {
                echo "<p>No results found. Try another search or use the randomizer below.</p>";
        }
        ?>

        <button id="randomizer-btn">Or try the randomizer</button>
        <div id="random-movie-result"></div>
    </main>


</body>

</html>