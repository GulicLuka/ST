<?php

require_once("model/UserDB.php");
require_once("ViewHelper.php");

class UserController {

    public static function showLoginForm() {
        ViewHelper::render("view/login.php", [
            "username" => ""
        ]);
    }

    public static function login() {
       if (UserDB::validLoginAttempt($_POST["username"], $_POST["geslo"])) {
            ViewHelper::redirect(BASE_URL . "home");
       } else {
            ViewHelper::render("view/login.php", [
                "errorMessage" => "Napačno uporabniško ime ali geslo!",
                "username" => $_POST["username"]
            ]);
       }
    }

    public static function showRegisterForm($data = [], $errors = []) {
        if (empty($data)) {
            $data = [
                "username" => "",
                "geslo" => "",
                "ime" => "",
                "priimek" => ""
            ];
        }

        if (empty($errors)) {
            foreach ($data as $key => $value) {
                $errors[$key] = "";
            }
            $errors["obstoj"] = ""; 
        }

        $vars = ["user" => $data, "errors" => $errors];
        ViewHelper::render("view/register.php", $vars);
    }

    public static function logout() {
        session_start();
        if(isset($_SESSION)) {
            session_destroy();
        } 
        ViewHelper::redirect(BASE_URL . "home");
    }

     public static function register() {
        $rules = [
            "username" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => ["regexp" => "/^[a-zA-ZšđčćžŠĐČĆŽ\.\-]+$/"]
            ],
            "geslo" =>  FILTER_SANITIZE_SPECIAL_CHARS,
            "ime" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => ["regexp" => "/^[a-zA-ZšđčćžŠĐČĆŽ]+$/"]
            ],
            "priimek" => [
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => ["regexp" => "/^[a-zA-ZšđčćžŠĐČĆŽ]+$/"]
            ]
        ];

        $data = filter_input_array(INPUT_POST, $rules); 

        $errors["username"] = $data["username"] === false ? "Napačen fomat uporabniškega imena, dovoljene so samo male in velike črke ter pika in pomišlaj" : "";
        $errors["geslo"] = strlen($data["geslo"]) < 8 ? "Vneseno geslo je prekratko, geslo mora biti dolgo vsaj 8 znakov" : "";
        $errors["ime"] = $data["ime"] === false ? "Vnesite vaše ime, dovoljene so samo male in velike črke" : "";
        $errors["priimek"] = $data["priimek"] === false ? "Vnesite vaš priimek, dovoljene so samo male in velike črke" : "";

        $isDataValid = true;
        foreach ($errors as $error) {
            $isDataValid = $isDataValid && empty($error);
        }

        if ($isDataValid) {
            if(UserDB::RegisterNewUser($data["username"], $data["ime"], $data["priimek"], $data["geslo"])) {
                ViewHelper::redirect(BASE_URL . "user/login");
            } else {
                $errors["obstoj"] = "Uporabnik z uporabniškim imenom " . $data["username"] . " že obstaja!";
                self::showRegisterForm($data, $errors);
            }
        } else {
            $errors["obstoj"] = "";
            self::showRegisterForm($data, $errors);
        }    
    }

}