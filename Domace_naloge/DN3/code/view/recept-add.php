<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "recept-add.css" ?>">
<meta charset="UTF-8" />
<title>Dodaj nov recept</title>
</head>
<body>
<?php  
    //session_start();
    if(!isset($_SESSION["username"])) {
        include("nav-guest.php");
    } else {
        include("nav-user.php");
    }

    if(isset($_SESSION["username"])) {
?>        
<div class="container">
<h1>Dodaj nov recept</h1>

<form action="<?= BASE_URL . "recepti/add" ?>"  method="post">
    <p class="important2"><?= $errors["obstoj"] ?></p>
    <div class="wrapper">
    <div class="block">
        <span class="pad">Naslov:</span>
        <span class="pad">Postopek priprave:</span>
        <span class="pad2">Čas priprave:</span>
    </div>
    <div class="block">
        <input type="text" name="naslov" value="<?= $recept["naslov"] ?>" pattern="^[ a-zA-ZšđčćžŠĐČĆŽ\.\-]+$" required autofocus /><span class="important"><?= $errors["naslov"] ?></span>
        <textarea name="postopek" rows="10" cols="40"><?= $recept["postopek"] ?></textarea>
        <input type="number" name="cas" value="<?= $recept["cas"] ?>" min ="0" required/><span class="important"><?= $errors["cas"] ?></span>
    </div>
</div>
    <button>Objavi recept</button>
</form>
</div>
<?php 
    } else {
?>
        <p>Za dodajanje novih receptov se je potrebno prijaviti.</p>
        <div>
            <a href="<?= BASE_URL . "user/register" ?>">Registracija</a>
            <a href="<?= BASE_URL . "user/login" ?>">Prijava</a>
        </div>
<?php
    }
?>
</body>
</html>


