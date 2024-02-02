<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type');

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/crud.class.php";
include_once "../classes/crud-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $tableName = $data["tableName"];
    $item = $data["item"];

    $newUpdate = new CrudContr($tableName, $item);
    $response = $newUpdate->updateItemData();


    // logging item for dev purpose
    $logMessage = is_array($data) ? json_encode($data) : $data;
    file_put_contents('debug.log', $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);

    echo json_encode(["success" => true]);
}