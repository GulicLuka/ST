<?php

require_once("model/ReceptiDB.php");
require_once("model/UserDB.php");
require_once("ViewHelper.php");

class ReceptiController {

    public static function index() {
        ViewHelper::render("view/home.php", ["recepti" => ReceptiDB::getNewest5()]);
    }
    
    // 1. prikaz uporabnikovih receptov, 2. prikaz iskanja, 3. editanje izbranega elementa, 4. odpri podrobnosti izbranega recepta, 5. prikazi vse
    public static function recepti() {
        if (isset($_GET["selectionId"])) {
            ViewHelper::render("view/recepti.php", ["recepti" => ReceptiDB::getAllReceptiFromUser($_GET["selectionId"])]);
        } else if (isset($_GET["query"])) {
            ViewHelper::render("view/recepti.php", ["recepti" => ReceptiDB::search($_GET["query"])]);
        } else if (isset($_GET["id"])) {
            ViewHelper::render("view/recept-edit.php", ["recept" => ReceptiDB::getRecept($_GET["id"])]);
        } else if (isset($_GET["idView"])) {
            $data["besedilo"] = "";
            $errors["besedilo"] = "";
            ViewHelper::render("view/recept-open.php", ["recept" => ReceptiDB::getRecept($_GET["idView"]), "komentarji" => ReceptiDB::getAllKomentarjiFromRecept($_GET["idView"]),"vnos" => $data, "errors" => $errors]);
        } else {
            ViewHelper::render("view/recepti.php", ["recepti" => ReceptiDB::getAll()]);
        }
    }

    public static function showAddReceptForm($data = [], $errors = []) {
        session_start();
        if(isset($_SESSION["username"])){
            if (empty($data)) {
                $data = [
                    "naslov" => "",
                    "postopek" => "",
                    "cas" => 0,
                ];
            }
    
            if (empty($errors)) {
                foreach ($data as $key => $value) {
                    $errors[$key] = "";
                }
                $errors["obstoj"] = "";
            }
            
            $vars = ["recept" => $data, "errors" => $errors];
            ViewHelper::render("view/recept-add.php", $vars);
        } else {
            ViewHelper::redirect(BASE_URL . "recepti");
        }
    }

    public static function addRecept() {
        session_start();
        if(isset($_SESSION["username"])) {
            $rules = [
                "naslov" => FILTER_SANITIZE_SPECIAL_CHARS,
                "postopek" => FILTER_SANITIZE_SPECIAL_CHARS,
                "cas" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($value) { return (is_numeric($value) && $value >= 0) ? floatval($value) : false; }
                ],
            ];
    
            $data = filter_input_array(INPUT_POST, $rules);
    
            $errors["naslov"] = empty($data["naslov"]) ? "Vnesite ime jedi" : "";
            $errors["postopek"] = empty($data["postopek"]) ? "Napišiti postopek priprave jedi" : "";
            $errors["cas"] = $data["cas"] === false ? "Čas priprave mora biti nenegativen" : "";
    
            $isDataValid = true;
            foreach ($errors as $error) {
                $isDataValid = $isDataValid && empty($error);
            }
    
            if ($isDataValid) {
                ReceptiDB::insert($data["naslov"], $data["postopek"], $data["cas"]);
                ViewHelper::redirect(BASE_URL . "recepti");
            } else {
                self::showAddForm($data, $errors);
            }
        } else {
            ViewHelper::redirect(BASE_URL . "recepti");
        }

    }

    public static function showEditReceptForm($data = [], $errors = []) {
        session_start();
        if(isset($_SESSION["username"])) {
            if (empty($data)) {
                $data = ReceptiDB::getRecept($_GET["id"]);
            }
    
            if (empty($errors)) {
                foreach ($data as $key => $value) {
                    $errors[$key] = "";
                }
            }
    
            $vars = ["recept" => $data, "errors" => $errors];
            ViewHelper::render("view/recept-edit.php", $vars);
        } else {
            ViewHelper::redirect(BASE_URL . "recepti/open?idView=" . $_GET["id"]);
        }
    }

    public static function editRecept() {
        if(!isset($_SESSION["username"])){
            session_start();
        }

        if(isset($_SESSION["username"])) {
            $rules = [  
                "recept_id" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => ["min_range" => 1]
                ],
                "naslov" => FILTER_SANITIZE_SPECIAL_CHARS,
                "postopek" => FILTER_SANITIZE_SPECIAL_CHARS,
                "cas" => [
                    "filter" => FILTER_CALLBACK,
                    "options" => function ($value) { return (is_numeric($value) && $value >= 0) ? floatval($value) : false; }
                ],
            ];
    
            $data = filter_input_array(INPUT_POST, $rules);
    
            $errors["recept_id"] = $data["recept_id"] === null ? "Napaka pri urejanju" : "";
            $errors["naslov"] = empty($data["naslov"]) ? "Vnesite ime jedi" : "";
            $errors["postopek"] = empty($data["postopek"]) ? "Napišiti postopek priprave jedi" : "";
            $errors["cas"] = $data["cas"] === false ? "Čas priprave mora biti nenegativen" : "";
    
            $isDataValid = true;
            foreach ($errors as $error) {
                $isDataValid = $isDataValid && empty($error);
            }
    
            if ($isDataValid) {
                ReceptiDB::update($data["recept_id"], $data["naslov"], $data["postopek"], $data["cas"]);
                ViewHelper::redirect(BASE_URL . "recepti");
            } else {
                self::showEditReceptForm($data, $errors);
            }
        } else {
            ViewHelper::redirect(BASE_URL . "recepti/open?idView=" . $_POST["recept_id"]);
        }
    }

    public static function deleteRecept() {
        session_start();
        if(isset($_SESSION["username"])) {
            $rules = [
                "id" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => ["min_range" => 1]
                ]
            ];
            $data = filter_input_array(INPUT_GET, $rules);
    
            $errors["id"] = $data["id"] === null ? "Napaka pri brisanju" : "";
            
            $isDataValid = true;
            foreach ($errors as $error) {
                $isDataValid = $isDataValid && empty($error);
            }
    
            if ($isDataValid) {
                ReceptiDB::delete($data["id"]);
            } 
    
            ViewHelper::redirect(BASE_URL . "recepti");
        } else {
            ViewHelper::redirect(BASE_URL . "recepti");
        }
    }

    public static function addKomentar() {
        session_start();
        if(isset($_SESSION["username"])){
            $rules = [
                "besedilo" => FILTER_SANITIZE_SPECIAL_CHARS,
                "recept_id" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => ["min_range" => 0]
                ],
                "user_id" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => ["min_range" => 0]
                ]
            ];
    
            $data = filter_input_array(INPUT_POST, $rules);
    
            $errors["besedilo"] = empty($data["besedilo"]) ? "Vnesite besedilo" : "";
            $errors["recept_id"] = $data["recept_id"] === null ? "Napaka pri dodajanju komentarja" : "";
            $errors["user_id"] = $data["user_id"] === null ? "Napaka pri dodajanju komentarja" : "";
    
            $isDataValid = true;
            foreach ($errors as $error) {
                $isDataValid = $isDataValid && empty($error);
            }
    
            if ($isDataValid) {
                ReceptiDB::insertKomentar($data["besedilo"], $data["recept_id"], $data["user_id"]);
                $vars = ["vnos" => $data, "errors" => $errors, "recept" => ReceptiDB::getRecept($data["recept_id"]), "komentarji" => ReceptiDB::getAllKomentarjiFromRecept($data["recept_id"])];
                ViewHelper::redirect(BASE_URL . "recepti/open?idView=" . $data["recept_id"]);
            } else {
                $vars = ["vnos" => $data, "errors" => $errors, "recept" => ReceptiDB::getRecept($data["recept_id"]), "komentarji" => ReceptiDB::getAllKomentarjiFromRecept($data["recept_id"])];
                ViewHelper::render("view/recept-open.php", $vars);
            }
        } else {
            $data["besedilo"] = "";
            $error["besedilo"] = "";
            $vars = ["vnos" => $data, "errors" => $errors, "recept" => ReceptiDB::getRecept($_POST["recept_id"]), "komentarji" => ReceptiDB::getAllKomentarjiFromRecept($_POST["recept_id"])];
            ViewHelper::render("view/recept-open.php", $vars);
        }

    }    

    public static function deleteKomentar() {
        session_start();
        if(isset($_SESSION["username"]) && $_SESSION["user_id"] == $_GET["user_id"]) {
            $rules = [
                "idKomentar" => [
                    "filter" => FILTER_VALIDATE_INT,
                    "options" => ["min_range" => 1]
                ]
            ];
            $data = filter_input_array(INPUT_GET, $rules);
    
            $errors["idKomentar"] = $data["idKomentar"] === null ? "Napaka pri brisanju" : "";
            
            $isDataValid = true;
            foreach ($errors as $error) {
                $isDataValid = $isDataValid && empty($error);
            }
    
            if ($isDataValid) {
                ReceptiDB::deleteKomentar($data["idKomentar"]);
            } 
            ViewHelper::redirect(BASE_URL . "recepti/open?idView=" . $_GET["idView"]);
        } else {
            ViewHelper::redirect(BASE_URL . "recepti/open?idView=" . $_GET["idView"]);
        }

    }
}

?>