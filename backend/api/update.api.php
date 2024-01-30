<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/update.class.php";
include_once "../classes/update-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : null;

    $data = new UpdateContr($tableName);
    $result = $data->fetchTableData();
    echo json_encode(["success" => true, "tableName" => $tableName, "tableData" => $result]);
}