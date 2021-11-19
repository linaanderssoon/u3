<?php

function sendJson($data = "msg", $statusCode = 200) {
    header("Content-Type: application/json");
    http_response_code($statusCode);

    echo json_encode(includes($data));
    exit();
}

function loadJson($filename) {
    if(file_exists($filename)){
        return json_decode(file_get_contents($filename), true);
    } else {
        return false;
    }
}

function saveJson($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json);

    return true;
}

function checkData($array) {
    foreach($array as $key => $value) {
        if($value == ""){
            sendJson([
                "code" => 12,
                "message" => "$key is empty"
            ], 404
            );
        }
    }
}

function checkLimit($array){
    $limit = $_GET["limit"];
    $chopped = array_slice($array, 0, $limit);

    includes($chopped);
    
    return $chopped;
    // exit();
}

function checkIds($array){
    //Hämtar idn som skickats med GET
    $ids = explode(",", $_GET["ids"]);
    $filteredById = [];
    
    //Gå igenom arrayen som tagits emot och kolla om de id:n som tagits emot i GET
    //finns i arrayen som tagits emot
    //Om de finns: Lägg till i filteredById
    foreach ($array as $index => $item) {
        if(in_array($item["id"], $ids)) {
            $filteredById[] = $item;
        }
    }

    //OM includes har skickats med, gå igenom de filtrerade objekten och byt ut owner ID till owner object.
    includes($filteredById);

    //Skicka tillbaka den filtrerade arrayen
    sendJson($filteredById);
    exit();  
}

function getOne($array) {
    $id = $_GET["id"];

    $foundID = false;

    foreach ($array as $index => $obj) {
        if ($obj["id"] == $id) {
            $foundID = true;

            if(isset($_GET["includes"])){
                $array[$index]["owner"] = getOwnerObj($obj["owner"]);
            }
            
            sendJson($array[$index]);
            exit();
        }
    }

    if($foundID == false) {
        sendJson(
            [
                "code" => 19,
                "message" => "This id does not exist"
            ],405
        );

    }
}

function filterStuff($array, $key, $value) {
    $filteredArray = [];

    foreach ($array as $element) {
        if($element["$key"] == $value) {
            $filteredArray[] = $element;
        }
    }

    includes($filteredArray);

    if(isset($_GET["limit"])) {
        sendJson(checkLimit($filteredArray));   
    }

    

    sendJson($filteredArray);
}

function getOwnerObj($ownerID){
    $jsonData = loadJson("database.json");
    $owners = $jsonData["owners"];
    
    foreach($owners as $owner) {
        if($owner["id"] === $ownerID){
            $ownerObj = $owner;
        }
    }
    
    return $ownerObj;
}

function includes($array) {
    if(isset($_GET["includes"])) {
        foreach($array as $index => $item) {
            $array[$index]["owner"] = getOwnerObj($item["owner"]);
        }
    }

    return $array;
}

function getHighestID($array) {
    $highestID = 0;

    foreach($array as $item) {
        if ($item["id"] > $highestID) {
            $highestID = $item["id"];
        }
    }
        
    return $highestID + 1;
}
?>