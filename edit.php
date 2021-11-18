<?php

require_once "functions.php";
error_reporting(-1);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

$jsonData = loadJson("database.json");

$animals = $jsonData["animals"];
$owners = $jsonData["owners"];

$data = file_get_contents("php://input");
$requestData = json_decode($data, true);


$found = false;
$foundOwner = null;
$foundAnimal = null;

function checkData($array) {
    foreach($array as $key => $value) {
        if($value == ""){
            sendJson([
                "code" => 1,
                "message" => "$key is empty"
            ], 404
            );
        }
    }
}


if($requestMethod === "PATCH") {    
    if(!isset($requestData["id"])) {
        sendJson([
            "code" => 2,
            "message" => "Missing 'id' of request body"
        ], 400
        );
    }

    checkData($requestData);
    $id = $requestData["id"];

    foreach($owners as $index => $owner) {
        if($owner["id"] == $id){
            $found = true;

            //- first name
            if(isset($requestData["first_name"])){
                $owner["first_name"] = $requestData["first_name"];
            }

            //- last name
            if(isset($requestData["last_name"])){
                $owner["last_name"] = $requestData["last_name"];
            }
            
            //-email
            if(isset($requestData["email"])){
                $owner["email"] = $requestData["email"];
            }
            
            //password
            if(isset($requestData["password"])){
                $owner["password"] = $requestData["password"];
            }


            $jsonData["owners"][$index] = $owner;
            $foundOwner = $owner;

            break;
        }
    }

        
    foreach($animals as $index => $animal) {
        if($animal["id"] == $id){
            $found = true;
    
            //- first name
            if(isset($requestData["name"])){
                $animal["name"] = $requestData["name"];
            }
    
            if(isset($requestData["animal"])){
                $animal["animal"] = $requestData["animal"];
            }
                
            if(isset($requestData["age"])){
                $animal["age"] = $requestData["age"];
            }
                
            //food
            if(isset($requestData["favourite_food"])){
                $animal["favourite_food"] = $requestData["favourite_food"];
            }

            //owner
            if(isset($requestData["owner"])){
                $animal["owner"] = $requestData["owner"];
            }
                
            $jsonData["animals"][$index] = $animal;
            $foundAnimal = $animal;
    
            break;
        }
    }

    if ($found === false) {
        send(
            [
                "code" => 5,
                "message" => "The owner or animal by `id` does not exist"
            ],404
        );
    }

    saveJson("database.json", $jsonData);

    if($foundAnimal !== null){
        sendJson($foundAnimal);
    } elseif($foundOwner !== null) {
        sendJson($foundOwner);
    }

    
}

?>