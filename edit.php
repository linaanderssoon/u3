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
            "code" => 9,
            "message" => "Missing 'id' of request body"
        ], 400 );
    }

    // Kontollera så att det som skickats med INTE är tomt
    checkData($requestData);
    $id = $requestData["id"];

    // Kontollera om det är OWNER eller ANIMAL som ska redigeras
    if(isset($requestData["editOwner"])){

        // Går igenom alla owners och hittar den som ska redigeras
        foreach($owners as $index => $owner) {
            if($owner["id"] == $id){
                $found = true;

                // Kolla vilka nycklas som ska redigeras och redigera dem
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

                // Uppdatera array
                $jsonData["owners"][$index] = $owner;
                $foundOwner = $owner;

                break;
            }
        }
    }

    // Kontollera om det är OWNER eller ANIMAL som ska redigeras
    if(isset($requestData["editAnimal"])){
        // Går igenom alla animals och hittar den som ska redigeras
        foreach($animals as $index => $animal) {
            if($animal["id"] == $id){
                $found = true;
        
                // Kolla vilka nycklas som ska redigeras och redigera dem
                // name
                if(isset($requestData["name"])){
                    $animal["name"] = $requestData["name"];
                }
        
                // aminal
                if(isset($requestData["animal"])){
                    $animal["animal"] = $requestData["animal"];
                }
                   
                // age
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
                  
                // Uppdatera array
                $jsonData["animals"][$index] = $animal;
                $foundAnimal = $animal;
        
                break;
            }
        }
    }

    // Om $found fortfarande ör false här, så har inget id hittats = finns ej i db
    if ($found === false) {
        sendJson(
            [
                "code" => 10,
                "message" => "The owner or animal by `id` does not exist"
            ],404
        );
    }

    // Spara ändringar till databas
    saveJson("database.json", $jsonData);

    // Skicka tillbaka meddelande till användare, med det som uppdaterats
    if($foundAnimal !== null){
        sendJson($foundAnimal);
    } elseif($foundOwner !== null) {
        sendJson($foundOwner);
    }

// Om fel metod

// Tanke: detta felmeddelande finns ju på varje sida. 
// Är det smartare att ha samma felkod på alla sidor, 
// eller vill man ha som vi har nu, olika på alla sidor?
} else {
    sendJson(
        [
            "code" => 11,
            "message" => "The method is not allowed"
        ],405
    );
}

?>