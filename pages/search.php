<body>
    <main id="searchpage">
        <form id="search-form" method="post" data-dashlane-rid="3436c57f04d655fc">
            <input type="text" name="search" placeholder="Movie title" required="" data-dashlane-rid="4b54f71f4de671f9">
            <button type="submit" data-dashlane-label="true" data-dashlane-rid="79914a06e655a776">Submit</button>
        </form>

        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['search'])) {
                $search = htmlspecialchars(trim($_POST['search']));
                GetMoviesByTitle($search);
            }
        }
        ?>


        <form method="post"> <!-- Remove action attribute to submit to the same script -->
            <input type="hidden" name="action" value="randomize"> <!-- Indicate action -->
            <button type="submit" id="randomizer-btn">Or try the randomizer</button>
        </form>

        <div id="random-movie-result"><?php getRandomMovie(); ?></div>
    </main>

</body>

</html>