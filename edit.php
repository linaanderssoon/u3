<?php

require_once "functions.php";
error_reporting(-1);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

$jsonData = loadJson("database.json");

//Vår array av djur eller owners
$animals = $jsonData["animals"];
$owners = $jsonData["owners"];

//Vår data
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

//Det vi hittar
$found = false;
$foundOwner = null;
$foundAnimal = null;


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

    if(isset($requestData["editOwner"])){
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
    }


    if(isset($requestData["editAnimal"])){
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

    }
    

    if ($found === false) {
        sendJson(
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

    
} else {
    sendJson(
        [
            "code" => 6,
            "message" => "The method is not allowed"
        ],405
    );
}

?>