<!-- POST -->

<?php
    require_once "functions.php";

    $contentType = $_SERVER["CONTENT_TYPE"];
    $method = $_SERVER["REQUEST_METHOD"];

    if($contentType !== "application/json") {
        header("content-Type: application/json");
        http_response_code(400);
        $json = json_encode(["messsage" => "Bad request"]);
        echo $json;
        exit();
    }

    if($method === "POST") {
        $data = file_get_contents("php://input");
        $requestData = json_decode($data, true);

        // Kolla om variablerna för att ska ny OWNER är satta
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
                        $jsonData = loadJson("database.json");
                        $highestId = 0;
                
                        foreach($jsonData["owners"] as $owner) {
                            if ($owner["id"] > $highestId) {
                                $highestId = $owner["id"];
                            }
                        }
                            
                        $newID = $highestId + 1;

                        $newOwner = [
                            "id" => $newID,
                            "first_name" => $firstName,
                            "last_name" => $lastName,
                            "email" => $email,
                            "password" => $password
                        ];
                            
                        array_push($jsonData["owners"], $newOwner);
                        saveJson("database.json", $jsonData);
                            
                        sendJson($newOwner, 201);

                    } else {
                        errorMsg("Invalid email");
                    }

                } else {
                    errorMsg("Your first name is too short");
                }
            } else {
                errorMsg("Something is empty");
            }

            

        } 

        // Kolla om variablerna för att skapa en ny ANIMAL är satt
        if(isset($requestData["animal"], $requestData["age"], $requestData["favourite_food"], $requestData["owner"], $requestData["name"])) {
            
            $animalType = $requestData["animal"];
            $age = $requestData["age"];
            $favourite_food = $requestData["favourite_food"];
            $owner = $requestData["owner"];
            $name = $requestData["name"];

            if(!empty($animalType) || !empty($age) || !empty($favourite_food) || !empty($owner) || !empty($name)){

                if($age < 30) {

                    $jsonData = loadJson("database.json");
                    $highestId = 0;
            
                    foreach($jsonData["animals"] as $animal) {
                        if ($animal["id"] > $highestId) {
                            $highestId = $animal["id"];
                        }
                    }

                    $newID = $highestId + 1;

                    $newAnimal = [
                        "id" => $newID,
                        "animal" => $animalType,
                        "name" => $name,
                        "age" => $age,
                        "favourite_food" => $favourite_food,
                        "owner" => $owner
                    ];
                        
                    array_push($jsonData["animals"], $newAnimal);
                    saveJson("database.json", $jsonData);
                        
                    sendJson($newAnimal, 201);

                } else {
                    if ($animalType == "Turtle"){

                        $jsonData = loadJson("database.json");
                        $highestId = 0;
                
                        foreach($jsonData["animals"] as $animal) {
                            if ($animal["id"] > $highestId) {
                                $highestId = $animal["id"];
                            }
                        }
    
                        $newID = $highestId + 1;

                        $newAnimal = [
                            "id" => $newID,
                            "animal" => $animalType,
                            "name" => $name,
                            "age" => $age,
                            "favourite_food" => $favourite_food,
                            "owner" => $owner
                        ];
                            
                        array_push($jsonData["animals"], $newAnimal);
                        saveJson("database.json", $jsonData);
                            
                        sendJson($newAnimal, 201);

                    } else {
                        errorMsg("Your animal can not be this old");
                    }
                }

            } else {
                errorMsg("Something is empty");
            }
        } else {
            errorMsg("You missed something");
        }

    } else {
        errorMsg("Method not allowed", 405);
    }    
?>