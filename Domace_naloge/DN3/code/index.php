<?php

require_once("controller/ReceptiController.php");
require_once("controller/UserController.php");

define("BASE_URL", $_SERVER["SCRIPT_NAME"] . "/");
define("IMAGES_URL", rtrim($_SERVER["SCRIPT_NAME"], "index.php") . "static/images/");
define("CSS_URL", rtrim($_SERVER["SCRIPT_NAME"], "index.php") . "static/css/");

$path = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";

$urls = [
    "home" => function() {
        ReceptiController::index();
    },
    "user/login" => function () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            UserController::login();
        } else {
            UserController::showLoginForm();
        }
    },
    "user/logout" => function () {
        UserController::logout();
    },
    "user/register" => function () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            UserController::register();
        } else {
            UserController::showRegisterForm();
        }
    },
    "recepti" => function () {
        ReceptiController::recepti();
    },
    "recepti/add" => function () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            ReceptiController::addRecept();
        } else {
            ReceptiController::showAddReceptForm();
        }
    },
    "recepti/open" => function () {
        ReceptiController::recepti();
    },
    "recepti/edit" => function () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            ReceptiController::editRecept();
        } else {
            ReceptiController::showEditReceptForm();
        }
    },
    "recepti/delete" => function () {
        ReceptiController::deleteRecept();
    },
    "recepti/deleteKomentar" => function () {
        ReceptiController::deleteKomentar();
    },
    "recepti/addKomentar" => function () {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            ReceptiController::addKomentar();
        } else {
            ReceptiController::recepti();
        }
    },
    "recepti/search" => function () {
        ReceptiController::recepti();
    },
    "" => function () {
        ViewHelper::redirect(BASE_URL . "home");
    },
];

try {
    if (isset($urls[$path])) {
       $urls[$path]();
    } else {
        echo "No controller for '$path'";
    }
} catch (Exception $e) {
    echo "An error occurred: <pre>$e</pre>";
    ViewHelper::error404();
} 
