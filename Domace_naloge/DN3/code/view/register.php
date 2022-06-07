<!DOCTYPE html>

<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "registracija.css" ?>">
<meta charset="UTF-8" />
<title>Registracija</title>

<?php  
    session_start();
    if(!isset($_SESSION["username"])) {
        include("nav-guest.php");
    } else {
        include("nav-user.php");
    }
?>
<div class="container">
<h1>Prijavite se</h1>

<form action="<?= BASE_URL . "user/register" ?>"  method="post">
    <p class="important2"><?= $errors["obstoj"] ?></p>
    <div class="wrapper">
    <div class="block">
        <span class="pad">Uporabniško ime:</span>
        <span class="pad">Geslo:</span>
        <span class="pad">Ime:</span>
        <span class="pad">Priimek:</span>
    </div>
    <div class="block">
        <input type="text" name="username" value="<?= $user["username"] ?>" pattern="^[a-zA-ZšđčćžŠĐČĆŽ\.\-]+$" required autofocus /><span class="important"><?= $errors["username"] ?></span>
        <input type="password" name="geslo" minlength="8"  required autofocus /><span class="important"><?= $errors["geslo"] ?></span>
        <input type="text" name="ime" value="<?= $user["ime"] ?>" pattern="^[a-zA-ZšđčćžŠĐČĆŽ]+$"  required/><span class="important"><?= $errors["ime"] ?></span>
        <input type="text" name="priimek" value="<?= $user["priimek"] ?>" pattern="^[a-zA-ZšđčćžŠĐČĆŽ]+$" required/><span class="important"><?= $errors["priimek"] ?></span>
    </div>
</div>
    <button>Registriraj se</button>
</form>
</div>

