<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "recepti.css" ?>">
<meta charset="UTF-8" />
<title>Recepti</title>
</head>
<body>
<?php  
    session_start();
    if(!isset($_SESSION["username"])) {
        include("nav-guest.php");
    } else {
        include("nav-user.php");
    }
?>
<h1 class="naslov">Vsi recepti</h1>

<form action="<?= BASE_URL . "recepti/search" ?>" method="get">
    <label for="query">Išči recepte:</label>
    <div>
        <input type="text" name="query" id="query" />
        <button>Išči</button>
    </div>
</form>

<?php
    if(isset($_SESSION["username"])) {
?>
    <div class="linkCall">
        <a href="<?= BASE_URL . "recepti?selectionId=" . $_SESSION["user_id"] ?>">Moji recepti</a>
        <a class="add" href="<?= BASE_URL . "recepti/add" ?>">Dodaj nov recept</a>
    </div>
<?php
    }
?>



<div class="container">
    <?php foreach ($recepti as $recept): 
        if(isset($_SESSION["username"])){
            if($_SESSION["role"] == "admin" || $_SESSION["user_id"] == $recept["user_id"]){
    ?>
            <div class="recept-container">
                <a href="<?= BASE_URL . "recepti/open?idView=" . $recept["recept_id"] ?>">
                    <p class="title"><?= $recept["naslov"] ?></p>
                    <p class="content"><span><?= $recept["ime"] ?> <?= $recept["priimek"] ?></span>   <span><?= $recept["datum"] ?></span></p>
                </a>
                <div class="inner">
                    <a href="<?= BASE_URL . "recepti/edit?id=" . $recept["recept_id"] ?>">Uredi</a>
                    <a href="<?= BASE_URL . "recepti/delete?id=" . $recept["recept_id"] ?>">Zbriši</a>
                </div>
            </div>

    <?php 
            } else {
    ?>
            <div class="recept-container">
                <a href="<?= BASE_URL . "recepti?idView=" . $recept["recept_id"] ?>">
                    <p class="title"><?= $recept["naslov"] ?></p>
                    <p class="content"><span><?= $recept["ime"] ?> <?= $recept["priimek"] ?></span>   <span><?= $recept["datum"] ?></span></p>
                </a>
            </div>
    <?php
            }
        } else {
    ?>
        <div class="recept-container">
            <a href="<?= BASE_URL . "recepti?idView=" . $recept["recept_id"] ?>">
                <p class="title"><?= $recept["naslov"] ?></p>
                <p class="content"><span><?= $recept["ime"] ?> <?= $recept["priimek"] ?></span>   <span><?= $recept["datum"] ?></span></p>
            </a>
        </div>
    <?php
        }
    endforeach; ?>
<div>
</body>
</html>

