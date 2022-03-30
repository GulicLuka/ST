<?php

require_once ("BookDB.php");

?><!DOCTYPE html>
<meta charset="UTF-8" />
<title>Book detail</title>

<?php $book = BookDB::get($_GET["id"]); ?>

<h1>Details about: <?= $book ?></h1>
<?php

# TODO: provide details about the book 

?>