<?php

function sendJson($data = "hej", $statusCode = 200) {
    header("Content-Type: application/json");
    http_response_code($statusCode);

    echo json_encode($data);
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

function inspect($varible) {
    echo "<pre>";
    var_dump($varible);
    echo "<pre>";
}

function errorMsg($msg, $code = 400) {
    header("Content-Type: application/json");
    http_response_code($code);
    $json = json_encode(["message" => "$msg"]);
    echo $json;
    exit();
}

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

function checkLimit($array){
    $limit = $_GET["limit"];
    $chopped = array_slice($array, 0, $limit);
    return $chopped;
    // exit();
}

function checkIds($array){
    
    $ids = explode(",", $_GET["ids"]);
    $filteredById = [];
    
    foreach ($array as $key) {
        if(in_array($key["id"], $ids)) {
            $filteredById[] = $key;
        }
    }
    
    // if($_GET["includes"] == true) {
    //     foreach($filteredById as $item) {
    //         $item["owner"] = getOwnerObj($item["id"]);
    //     }

    // }

    sendJson($filteredById);
    exit();
    
}

function getOne($array) {
    $id = $_GET["id"];

    foreach ($array as $index => $obj) {
        if ($obj["id"] == $id) {
            if(isset($_GET["includes"])){
                $array[$index]["owner"] = getOwnerObj($obj["owner"]);
            }
            
            sendJson($array[$index]);
            exit();
        }
    }
}

function filterStuff($array, $key, $value) {
    $filteredArray = [];

    foreach ($array as $element) {
        if($element["$key"] == $value) {
            $filteredArray[] = $element;
        }
    }

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


?>