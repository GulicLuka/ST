<?php

require_once ("BookDB.php");

?><!DOCTYPE html>
<meta charset="UTF-8" />
<title>Book detail</title>
<style>
    div{
        border: 1px solid blue;
        padding: 10px;
        width: fit-content;
        height: fit-content;
    }
</style>
<?php $book = BookDB::get($_GET["id"]); ?>
<div>
    <h1>Details about: <?= $book->title ?></h1>
    <ul>
        <li>Author: <b><?= $book->author ?></b></li>
        <li>Title: <b><?= $book->title ?></b></li>
        <li>Price: <b><?= $book->price ?> EUR</b></li>
    </ul>
</div>
<?php

# TODO: provide details about the book 

?>