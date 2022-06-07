<?php

require_once "DBInit.php";

class UserDB {

    public static function validLoginAttempt($username, $geslo) {
        $dbh = DBInit::getInstance();
        $geslo = sha1($geslo, false);
        $statement = $dbh->prepare("SELECT user_id, username, ime, priimek, role FROM user WHERE username = :username AND geslo = :geslo");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":geslo", $geslo);
        $statement->execute();
        $user = $statement->fetch();

        if ($user != null && !isset($_SESSION["username"])) {
            echo "user exists - setting up session";
            session_start();
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["ime"] = $user["ime"];
            $_SESSION["priimek"] = $user["priimek"];
            $_SESSION["role"] = $user["role"];
            var_dump($_SESSION);
            return true;
        } else {
            return false;
        }
    }

    public static function RegisterNewUser($username, $ime, $priimek, $geslo) {
        try {
            $geslo = sha1($geslo, false);
            $db = DBInit::getInstance();
                $statement = $db->prepare("INSERT INTO user (username, ime, priimek, geslo) 
                    VALUES (:username, :ime, :priimek, :geslo)");
                $statement->bindParam(":username", $username);
                $statement->bindParam(":ime", $ime);
                $statement->bindParam(":priimek", $priimek);
                $statement->bindParam(":geslo", $geslo);
                $statement->execute();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
