<?php
    
require_once "functions.php";
error_reporting(-1);
$requestMethod = $_SERVER["REQUEST_METHOD"];

$jsonData = loadJson("database.json");
$owners = $jsonData["owners"];
$animals = $jsonData["animals"];

if($requestMethod === "GET") {
    if(isset($_GET["checkOwner"])){
        if(isset($_GET["limit"])) {
            checkLimit($owners);
        }

        if(isset($_GET["ids"])){
            checkIds($owners);
        }

        
        if(isset($_GET["id"])) {
            getOne($owners);
        }

        sendJson($owners);
    }

    if(isset($_GET["checkAnimal"])){
        if(isset($_GET["limit"])) {
            checkLimit($animals);
        }

        if(isset($_GET["ids"])){
            checkIds($animals);
        }

        
        if(isset($_GET["id"])) {
            getOne($animals);
        }

        sendJson($animals);
    }

    // $contentType = $_SERVER["CONTENT_TYPE"];
    // if ($contentType !== "application/json") {
    //     sendJson(
    //         ["message" => "The API only accepts JSON"],
    //         400
    //     );
    // }

}else {
    sendJson(
        [
            "code" => 6,
            "message" => "The method is not allowed"
        ],405
    );
}


?>