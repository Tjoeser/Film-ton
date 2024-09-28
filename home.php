<body>

<h1>HOMEPAGE</h1>
<h3>Genres</h3>
<h5>Horror</h5>
<?php
echo filterMovies(['Genre' => 'Horror']);
?>
<h5>Action</h5>
<?php
echo filterMovies(['Genre' => 'Action']);
?>
<h5>Romance</h5>
<?php
echo filterMovies(['Genre' => 'Romance']);
?>

</body>