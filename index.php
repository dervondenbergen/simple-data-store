<?php

header('Content-Type: application/json');

function jsonError($message) {
    return json_encode(array(
        "error" => $message
    ));
}

$method = $_SERVER["REQUEST_METHOD"];

$project = $_GET["project"];

if (isset($project)) {
    
    $filename = "data/".$project.".json";
    
    if (file_exists($filename)) {
        $file = file_get_contents($filename);
        
        $json = json_decode($file);
        if (!is_null($json) && !is_bool($json)) {
            $data = $json;
        } else {
            http_response_code(500);
            echo jsonError("file couldn't be read, json malformed");
            exit();
        }
    } else {
        $data = new stdClass();
    } 

    $id = $_GET["id"];
    $newValue = $_GET["value"];

    if (isset($id)) {
        if ($method == "POST" && isset($newValue)) {
            $data->{$id} = $newValue;
            file_put_contents($filename, json_encode($data));
        }  
        header('Content-Type: plain/text');
        $value = $data->{$id};
        if (!isset($value)) {
            http_response_code(404);
        }
        echo strval($value); 
    } else {
        if ($method == "GET") {
            echo json_encode($data);
            exit();
        }
        if ($method == "POST") {
            file_put_contents($filename, json_encode($data));
            exit();            
        }
    }
    
} else {
    http_response_code(400);
    echo jsonError("'project' is missing");
    exit();
}

?>
