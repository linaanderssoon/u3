<!-- DELETE -->

<?php

    require_once "functions.php";

    $method = $_SERVER["REQUEST_METHOD"];
    $contentType = $_SERVER["CONTENT_TYPE"];

    if ($method === "OPTIONS") {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        exit();
    } 

    header("Access-Control-Allow-Origin: *");

    if($contentType !== "application/json") {
        errorMsg("The API only accepts json");
        exit();
    }

    $jsonData = loadJson("database.json");
    $animals = $jsonData["animals"];


    if($method === "DELETE") {
        $data = file_get_contents("php://input");
        $requestData = json_decode($data, true);

        if(isset($requestData["id"])) {
            $id = $requestData["id"];
            $found = false;

            foreach($jsonData["animals"] as $index => $animal) {
                if($animal["id"] == $id) {
                    $found = true;
                    array_splice($jsonData["animals"], $index, 1);
                    break;
                }
            }

            if($found === false) {
                sendJson("This animal does not exist");
            }

            saveJson("database.json", $jsonData);
            sendJson("Removed animal $id.");

        } else {
            errorMsg("Id is required if you want to delete someone");
        }

    } else {
        errorMsg("Method not allowed");
    }
?>