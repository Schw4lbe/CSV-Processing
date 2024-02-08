<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
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
    $tableName = strtolower($data["tableName"]);
    $item = $data["item"];
    $itemId = $item["id"];

    $newUpdate = new CrudContr($tableName, $item, $itemId);
    $response = $newUpdate->updateItemData();

    if (!$response) {
        error_log("error in updateItem: $item" . PHP_EOL, 3, "../logs/app-error.log");
        header('Content-Type: application/json');
        echo json_encode(["success" => false]);
        exit();
    }
    header('Content-Type: application/json');
    echo json_encode(["success" => true]);


} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER["PATH_INFO"] === "/add") {
        $tableName = strtolower($data["tableName"]);
        $item = $data["item"];

        $newItem = new CrudContr($tableName, $item, null);
        $response = $newItem->addNewItem();

        if (!$response) {
            error_log("error in addItem: $item" . PHP_EOL, 3, "../logs/app-error.log");
            header('Content-Type: application/json');
            echo json_encode(["success" => false]);
            exit();
        }
        header('Content-Type: application/json');
        echo json_encode(["success" => true]);


    } else if ($_SERVER["PATH_INFO"] === "/delete") {
        $tableName = strtolower($data["tableName"]);
        $itemId = $data['itemId'];

        $newDelete = new CrudContr($tableName, null, $itemId);
        $response = $newDelete->deleteItem();

        if (!$response) {
            error_log("error in deleteItem: $itemId" . PHP_EOL, 3, "../logs/app-error.log");
            header('Content-Type: application/json');
            echo json_encode(["success" => false]);
            exit();
        }
        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
    }

} else {
    header('Content-Type: application/json');
    echo json_encode(["success" => false]);
}