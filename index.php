<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

function jsonError($message) {
    return json_encode(array(
        "error" => $message
    ));
}

function getParameter($param) {
    $method = $_SERVER["REQUEST_METHOD"];

    if ($method == "GET") {
        return $_GET[$param];
    }
    if ($method == "POST") {
        return $_POST[$param]; 
    }
}

$project = getParameter("project");

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

    $id = getParameter("id");
    $newValue = getParameter("value");
    if (is_bool(json_decode($newValue))) {
        $newValue = json_decode($newValue);
    } elseif (is_numeric(json_decode($newValue))) {
        $newValue = $newValue + 0;
    }

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
        echo is_bool($value) ? json_encode($value) : strval($value);
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
