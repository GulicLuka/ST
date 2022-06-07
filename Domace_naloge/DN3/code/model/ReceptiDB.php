<?php

require_once "DBInit.php";

class ReceptiDB {

    public static function getNewest5() {
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT r.recept_id,  r.datum, r.naslov, r.postopek, r.cas, r.user_id, u.ime, u.priimek
            FROM recept r, user u WHERE r.user_id = u.user_id ORDER BY r.recept_id DESC LIMIT 5");
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function getAll() {       // DOBI VSE RECEPTE 
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT r.recept_id,  r.datum, r.naslov, r.postopek, r.cas, r.user_id, u.ime, u.priimek
            FROM recept r, user u WHERE r.user_id = u.user_id");
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function getRecept($id) { // PRIDOBI SAMO TISTEGA Z ENAKIM recept_id -- UREJANJE ZAPISOV V TABELI RECEPT
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT recept_id, naslov, postopek, cas, user_id
            FROM recept  WHERE recept_id = :recept_id");
        $statement->bindParam(":recept_id", $id, PDO::PARAM_INT);
        $statement->execute();

        $recept = $statement->fetch();

        if ($recept != null) {
            return $recept;
        } else {
            throw new InvalidArgumentException("No record with id $id");
        }
    }

    public static function getAllReceptiFromUser($user_id) { // PRIDOBI VSE RECEPTE, KI JIH JE NEK USER USTVARIL
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT r.recept_id,  r.datum, r.naslov, r.postopek, r.cas, r.user_id, u.ime, u.priimek
            FROM recept r, user u  WHERE r.user_id = u.user_id AND u.user_id = :user_id");
        $statement->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();;
    }

    public static function insert($naslov, $postopek, $cas) { //VNOS PODATKOV
        $db = DBInit::getInstance();
        session_start();
        if(isset($_SESSION["username"])){
            $statement = $db->prepare("INSERT INTO recept (naslov, postopek, cas, user_id) 
                VALUES (:naslov, :postopek, :cas, :user_id)");
            $statement->bindParam(":naslov", $naslov);
            $statement->bindParam(":postopek", $postopek);
            $statement->bindParam(":cas", $cas);
            $statement->bindParam(":user_id", $_SESSION["user_id"]);
            $statement->execute();
        }
    }

    public static function update($recept_id, $naslov, $postopek, $cas) { // POSODOBI RECEPT
        $db = DBInit::getInstance();

        $statement = $db->prepare("UPDATE recept SET naslov = :naslov, postopek = :postopek, cas = :cas
            WHERE recept_id = :recept_id");
        $statement->bindParam(":naslov", $naslov);
        $statement->bindParam(":postopek", $postopek);
        $statement->bindParam(":cas", $cas);
        $statement->bindParam(":recept_id", $recept_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function delete($recept_id) { // ZBRISI RECEPT
        $db = DBInit::getInstance();

        $statement = $db->prepare("DELETE FROM recept WHERE recept_id = :recept_id");
        $statement->bindParam(":recept_id", $recept_id, PDO::PARAM_INT);
        $statement->execute();
    } 

    public static function search($query) { // ISKANJE RECEPTOV IZ SEARCHA
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT r.recept_id,  r.datum, r.naslov, r.postopek, r.cas, r.user_id, u.ime, u.priimek
            FROM recept r, user u WHERE r.user_id = u.user_id AND (naslov LIKE :query OR postopek LIKE :query)"); //MATCH(naslov) AGAINST (:query IN BOOLEAN MODE) OR MATCH(postopek) AGAINST (:query IN BOOLEAN MODE)
        $statement->bindValue(":query", "%".$query."%");
        $statement->execute();

        return $statement->fetchAll();
    }   
    
    public static function getAllKomentarjiFromRecept($recept_id) { // Pridobi vse komentarja recepta
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT k.komentar_id,  u.ime, u.priimek, k.besedilo, u.user_id
            FROM recept r, user u, komentarji k  WHERE r.recept_id = k.recep_id AND u.user_id = k.user_id AND r.recept_id = :recept_id");
        $statement->bindValue(":recept_id", $recept_id);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function insertKomentar($besedilo, $recept_id, $user_id) { // Vnos komentarjev
        $db = DBInit::getInstance();

        $statement = $db->prepare("INSERT INTO komentarji (besedilo, user_id, recep_id) 
            VALUES (:besedilo, :user_id, :recep_id)");
        $statement->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
        $statement->bindParam(":recep_id", $recept_id, PDO::PARAM_INT);
        $statement->bindParam(":besedilo", $besedilo);
        $statement->execute();
    }

    public static function deleteKomentar($komentar_id) { // Zbrisi komentar
        $db = DBInit::getInstance();

        $statement = $db->prepare("DELETE FROM komentarji WHERE komentar_id = :komentar_id");
        $statement->bindParam(":komentar_id", $komentar_id, PDO::PARAM_INT);
        $statement->execute();
    } 
}