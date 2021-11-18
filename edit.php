<!-- PATCH -->

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


$id = $requestData["id"];
$found = false;
$foundOwner = null;
$foundAnimal = null;


if($requestMethod === "PATCH") {
    if(!isset($requestData["id"])) {
        sendJson([
            "code" => 1,
            "message" => "Missing 'id' of request body"

            ], 400
        );
    }

    foreach($owners as $index => $owner) {
        if($owner["id"] == $id){
            $found = true;

            //- first name
            if(isset($requestData["first_name"])){
                $user["first_name"] = $requestData["first_name"];
            }

            //- last name
            if(isset($requestData["last_name"])){
                $user["last_name"] = $requestData["last_name"];
            }
            
            //-email
            if(isset($requestData["email"])){
                $user["email"] = $requestData["email"];
            }
            
            //password
            if(isset($requestData["password"])){
                $user["password"] = $requestData["password"];
            }
            
            $owners[$index] = $owner;
            $foundOwner = $owner;

            break;
        }

        foreach($animals as $index => $animal) {
            if($animal["id"] == $id){
                $found = true;
    
                //- first name
                if(isset($requestData["name"])){
                    $user["name"] = $requestData["name"];
                }
    
                //- last name
                if(isset($requestData["animal"])){
                    $user["animal"] = $requestData["animal"];
                }
                
                //-email
                if(isset($requestData["age"])){
                    $user["age"] = $requestData["age"];
                }
                
                //password
                if(isset($requestData["favourite_food"])){
                    $user["favourite_food"] = $requestData["favourite_food"];
                }

                //owner
                if(isset($requestData["owner"])){
                    $user["owner"] = $requestData["owner"];
                }
                
                $animals[$index] = $animal;
                $foundAnimal = $animal;
    
                break;
            }
        }
    }
}

?>