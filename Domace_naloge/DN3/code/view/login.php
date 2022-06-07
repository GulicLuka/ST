<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "navUser.css" ?>">
<link rel="stylesheet" type="text/css" href="<?= CSS_URL . "login.css" ?>">
<meta charset="UTF-8" />
<title>Prijava</title>
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
<div class="container">
<h1>Prijava</h1>

<form action="<?= BASE_URL . "user/login" ?>"  method="post">
    <?php if (!empty($errorMessage)): ?>
        <p class="important"><?= $errorMessage ?></p>
    <?php endif; ?>
    <div class="wrapper">
    <div class="block">
        <span>Uporabniško ime:</span>
        <span>Geslo:</span>
    </div>
    <div class="block">
        <input type="text" name="username" value="<?= $username ?>" pattern="^[a-zA-ZšđčćžŠĐČĆŽ\.\-]+$" required autofocus />
        <input type="password" name="geslo" required autofocus />
    </div>
    </div>
    <button>Prijava</button>
</form>
<p><a href="<?= BASE_URL . "user/register" ?>">Ali še nimate računa?</a></p>
</div>
</body>
</html>

