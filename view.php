<?php
    
require_once "functions.php";
error_reporting(-1);
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($method === "OPTIONS") {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    exit();
} 

header("Access-Control-Allow-Origin: *");

$jsonData = loadJson("database.json");
$owners = $jsonData["owners"];
$animals = $jsonData["animals"];

if($requestMethod === "GET") {
    if(isset($_GET["checkOwner"])){

        // Om det är fler än 1 id är satta, hämta de ägarna
        if(isset($_GET["ids"])){
            checkIds($owners);
        }
        
        // Om ett id är angett, hämta den ägaren
        if(isset($_GET["id"])) {
            getOne($owners);
        }

        if(isset($_GET["first_name"])){
            $value = $_GET["first_name"];
            filterStuff($owners, "first_name", $value);
        }

        //Om limit finns, hämta så många ägare
        if(isset($_GET["limit"])) {
            sendJson(checkLimit($owners));
            exit();
        }
        //Annars hämta alla ägare
        sendJson($owners);
    }

    if(isset($_GET["checkAnimal"])){

        //Om det är fler än 1 id är satta, hämta de djuren 
        if(isset($_GET["ids"])){
            checkIds($animals);
        }

        // Om ett id är angett, hämta det djuret
        if(isset($_GET["id"])) {
            getOne($animals);
        }

        if(isset($_GET["age"])){
            $value = $_GET["age"];
            filterStuff($animals, "age", $value);
        }
        
        if(isset($_GET["animal"])){
            $value = $_GET["animal"];
            filterStuff($animals, "animal", $value);
        }

        //Om limit finns, hämta så många djur
        if(isset($_GET["limit"])) {
            sendJson(checkLimit($animals));
            exit();
        }
        
        //Annars hämta alla djur
        sendJson($animals);
    }


}else {
    sendJson(
        [
            "code" => 6,
            "message" => "The method is not allowed"
        ],405
    );
}


?>