<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/fetch.class.php";
include_once "../classes/fetch-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : null;
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    $itemsPerPage = isset($_GET['itemsPerPage']) ? $_GET['itemsPerPage'] : null;
    $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : null;

    // Create a message string with the variable values
    $logMessage = "tableName: " . $tableName . ", page: " . $page . ", itemsPerPage: " . $itemsPerPage . ", sortBy: " . $sortBy;

    // Log the message to a file named debug.log in the current directory
    file_put_contents("debug.log", $logMessage . "\n", FILE_APPEND);


    $newFetch = new FetchContr($tableName, $page, $itemsPerPage, $sortBy);
    $tableData = $newFetch->fetchTableData();
    echo json_encode(["success" => true, "tableData" => $tableData]);
}