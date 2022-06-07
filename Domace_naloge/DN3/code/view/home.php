<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "home.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<meta charset="UTF-8" />
<title>Kuharski recepti</title>
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

<h1 class="naslov">Skuhajte si nekaj okusnega</h1>
<div class="featured-container">
    <h2 class="podnaslov">Najnovejši recepti</h2>
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
        </div>
</div>
<div class="block">
    <h2>Neveste kaj bi danes jedli?</h2>
    <p>Ni panike s klikom na gumb lahko prebrskate po celotni naši zbirki okusnih receptov.</p>
    <a href="<?= BASE_URL . "recepti" ?>">Vsi recepti</a>
</div>
</body>
</html>