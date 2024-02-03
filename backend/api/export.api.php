<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
// header('Content-Disposition: attachment; filename="export.csv"');

// included classes
include_once "../classes/dbh.class.php";
include_once "../classes/export.class.php";
include_once "../classes/export-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $tableName = isset($_GET['tableName']) ? $_GET['tableName'] : null;
    file_put_contents('debug.log', $tableName . PHP_EOL, FILE_APPEND);

    $newExport = new ExportContr($tableName);
    $response = $newExport->exportData();

    if (!$response) {
        error_log("exportData call failed with tableName: $tableName" . PHP_EOL, 3, "../logs/app-error.log");
        // Specify header for content type as JSON for error message consistency.
        header('Content-Type: application/json');
        echo json_encode(["success" => false]);
        exit();
    }
}