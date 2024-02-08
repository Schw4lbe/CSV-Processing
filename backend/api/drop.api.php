<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/drop.class.php";
include_once "../classes/drop-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $tableName = strtolower($data);

    $newDrop = new DropContr($tableName);
    $result = $newDrop->dropTable();

    if (!$result) {
        error_log("error on dropTable method: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
        header('Content-Type: application/json');
        echo json_encode(["success" => false]);
        exit();
    }
    header('Content-Type: application/json');
    echo json_encode(["success" => true]);
}