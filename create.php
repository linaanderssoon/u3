<?php
    require_once "functions.php";
    error_reporting(-1);

    $contentType = $_SERVER["CONTENT_TYPE"];
    $method = $_SERVER["REQUEST_METHOD"];

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

    // Kontollera om request method är POST
    if($method === "POST") {
        $data = file_get_contents("php://input");
        $requestData = json_decode($data, true);

        // Om vaiablerna för OWNER är satta ska en ny OWNER skapas
        if(isset($requestData["first_name"], $requestData["last_name"], $requestData["email"], $requestData["password"])) {

            $firstName = $requestData["first_name"];
            $lastName = $requestData["last_name"];
            $email = $requestData["email"];
            $password = $requestData["password"];

            // Kolla om de innehåller något
            if(!empty($firstName) || !empty($lastName) || !empty($email) || !empty($password)) {
                
                // Kolla om namet är mer än två tecken
                if(strlen($firstName) > 2) {
                    
                    // Kolla om det är em giltig email
                    if(strpos($email, "@") !== false) {

                        // Om vi kommit hit är allt okej, 
                        // skapa ny owner
                        $jsonData = loadJson("database.json");
                            
                        $newOwner = [
                            "id" => getHighestID($jsonData["owners"]),
                            "first_name" => $firstName,
                            "last_name" => $lastName,
                            "email" => $email,
                            "password" => $password
                        ];
                            
                        // Pusha in ny använare i databasen
                        array_push($jsonData["owners"], $newOwner);
                        saveJson("database.json", $jsonData);
                            
                        // Skicka meddelande till användaren
                        sendJson($newOwner, 201);

                    } else {
                        sendJson([
                            "code" => 1,
                            "message" => "Invalid email."
                        ], 400);
                    }
                
                } else {
                    sendJson([
                        "code" => 2,
                        "message" => "Your first name is too short."
                    ], 400);
                }
            } else {
                sendJson([
                    "code" => 3,
                    "message" => "You have to fill all the fields."
                ], 400);
            }
        } 

        // Om variablerna för ANIMAL är satta, skapa ANIMAL
        if(isset($requestData["animal"], $requestData["age"], $requestData["favourite_food"], $requestData["owner"], $requestData["name"])) {
            $animalType = $requestData["animal"];
            $age = $requestData["age"];
            $favourite_food = $requestData["favourite_food"];
            $owner = $requestData["owner"];
            $name = $requestData["name"];

            // Kontollera så allt är ifyllt
            if(!empty($animalType) || !empty($age) || !empty($favourite_food) || !empty($owner) || !empty($name)) {

                $jsonData = loadJson("database.json");
                $owners = $jsonData["owners"];

                // Kontollera att owners som skickats med existerar existerar
                $ownerIDs = array_column($owners, "id");

                // Om den ägeren som skickats med finns
                if(in_array($owner, $ownerIDs)) {
                    // Kontollera så att ålderna är rimlig, inte mer än 30
                    if($age < 30) {
    
                        // Om allt är ok, skapa nytt djur
                        $newAnimal = [
                            "id" => getHighestID($jsonData["animals"]),
                            "animal" => $animalType,
                            "name" => $name,
                            "age" => $age,
                            "favourite_food" => $favourite_food,
                            "owner" => $owner
                        ];
                            
                        // Pusha in nya djuret i databasen
                        array_push($jsonData["animals"], $newAnimal);
                        saveJson("database.json", $jsonData);
                          
                        // Skicka meddelande till användaren
                        sendJson($newAnimal, 201);
    
                    } else {
                        // Om det är en sköldpadda får den vara mer än 30år :D
                        if ($animalType == "Turtle") {
                            $jsonData = loadJson("database.json");
    
                            $newAnimal = [
                                "id" => getHighestID($jsonData["animals"]),
                                "animal" => $animalType,
                                "name" => $name,
                                "age" => $age,
                                "favourite_food" => $favourite_food,
                                "owner" => $owner
                            ];
                                
                            // Pusha in sköldpaddan i databasen
                            array_push($jsonData["animals"], $newAnimal);
                            saveJson("database.json", $jsonData);
                                
                            // Skicka meddelande til användaren
                            sendJson($newAnimal, 201);
    
                        } else {
                            sendJson([
                                "code" => 4,
                                "message" => "Your animal can not be this old."
                            ], 400);
                        }
                    }

                } else {
                    sendJson([
                        "code" => 5,
                        "message" => "This owner does not exist."
                    ], 400);
                }
                
            } else {
                sendJson([
                    "code" => 6,
                    "message" => "You have to fill all the fields."
                ], 400);
            }

        } else {
            sendJson([
                "code" => 7,
                "message" => "You missed somtehing."
            ], 400);
        }

    } else {
        sendJson([
            "code" => 8,
            "message" => "Method not allowed."
        ], 405 );
    }    
?>