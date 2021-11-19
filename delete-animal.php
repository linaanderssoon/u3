<?php
// Om an ska ta bort en ägare/djur baserat på bara id,
// måste du ju veta var id:t kommer ifrån. Aningen gjorde vi såhär (delete),
// med två separata filer, dock är ju koden typ likadan, så det känns lite onödigt.
// Är det att föredra att använda en nyckel för att separera vad id kommer ifrån, 
// som vi gjort på edit.php 

    require_once "functions.php";

    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];

    if ($method === "OPTIONS") {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        exit();
    } 

    header("Access-Control-Allow-Origin: *");

    // Kontollera content-type
    if($contentType !== "application/json") {
        sendJson([
            "code" => 13,
            "message" => "The API only accepts json"
        ], 400
        );
    }

    $jsonData = loadJson("database.json");

    // Kontollera att det är rätt metod
    if($method === "DELETE") {
        $data = file_get_contents("php://input");
        $requestData = json_decode($data, true);

        // Kolla om ett id skickats med
        if(isset($requestData["id"])) {
            $id = $requestData["id"];
            $found = false;

            // Hitta djuret som ska tas bort
            foreach($jsonData["animals"] as $index => $animal) {
                if($animal["id"] == $id) {
                    // Splica bort djuret och sätt found till true
                    $found = true;
                    array_splice($jsonData["animals"], $index, 1);
                    break;
                }
            }

            // Om found är falskt här, så har inget djur hittats
            if($found === false) {
                sendJson([
                    "code" => 14,
                    "message" => "This animal does not exit"
                ], 404);
            }

            // Spara ändringar + felmeddelande
            saveJson("database.json", $jsonData);
            sendJson("Removed animal $id.");

        } else {
            // Om inget id skickats med skicka felmeddelande
            sendJson([
                "code" => 15,
                "message" => "Id is required if you want to delete someone"
            ], 400);
        }

    } else {
        // Fel metod
        sendJson([
            "code" => 16,
            "message" => "Method not allowed"
        ], 400);
    }
?>