<!DOCTYPE html>

<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "view.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<meta charset="UTF-8" />
<title><?= $recept["naslov"] ?></title>

<?php  
    session_start();
    if(!isset($_SESSION["username"])) {
        include("nav-guest.php");
    } else {
        include("nav-user.php");
    }
?>

<h1 class="naslov"><?= $recept["naslov"] ?></h1>
<p class="cas">Čas priprave: <?= $recept["cas"] ?></p>
<p class="postopek"><?= $recept["postopek"] ?></p>

<div class="container-links">
<?php
    if(isset($_SESSION["username"])){
        if($_SESSION["user_id"] == $recept["user_id"] || $_SESSION["role"] == "admin") {
?>
            <a class="edit" href="<?= BASE_URL . "recepti/edit?id=" . $recept["recept_id"] ?>">Uredi recept</a>
<?php       
        }
    }
?>

<a class="edit" href="<?= BASE_URL . "recepti" ?>">Vrni se na recepte</a>
</div>
<?php
    if(isset($_SESSION["username"])){
?>
<div class="addKomentar">
    <form action="<?= BASE_URL . "recepti/addKomentar" ?>" method="post">
        <input type="hidden" name="recept_id" value="<?= $recept["recept_id"] ?>" />
        <input type="hidden" name="user_id" value="<?= $_SESSION["user_id"] ?>" />
        <p>
            <label>Vaš komentar:</label>
            <div>
                <input type="text" name="besedilo" value="<?= $vnos["besedilo"] ?>" />
                <span class="important"><?= $errors["besedilo"] ?></span>

                <button>Objavi komentar</button>
            </div>
        </p>
    </form>
</div>
<?php       
    }
?>

<div class="komentarji-container">
<?php foreach ($komentarji as $komentar): 
        if(isset($_SESSION["username"])){
            if($_SESSION["role"] == "admin" || $_SESSION["user_id"] == $komentar["user_id"]){
    ?>
                <div class="komentar">
                    <div class="inner-komentar">
                        <p class="ime"><?= $komentar["ime"] ?> <?= $komentar["priimek"] ?></p>
                        <p class="besedilo"><?= $komentar["besedilo"] ?></p>
                    </div>
                    <a class="delete" href="<?= BASE_URL . "recepti/deleteKomentar?idKomentar=" . $komentar["komentar_id"] . "&" . "idView=" . $recept["recept_id"] . "&user_id=" . $recept["user_id"]?>">Zbriši komentar</a>
                </div>
    <?php 
            } else {
    ?>
                <div class="komentar">
                    <p class="ime"><?= $komentar["ime"] ?> <?= $komentar["priimek"] ?></p>
                    <p class="besedilo"><?= $komentar["besedilo"] ?></p>
                </div>
    <?php
            }
        } else {
    ?>
            <div class="komentar">
                <p class="ime"><?= $komentar["ime"] ?> <?= $komentar["priimek"] ?></p>
                <p class="besedilo"><?= $komentar["besedilo"] ?></p>
            </div>
    <?php
        }
    endforeach; ?>
</div>
