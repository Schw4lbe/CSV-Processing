<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// TODO: included classes

// Handle preflight request for OPTIONS method from browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // check if file is set in superglobal
    if (isset($_FILES["file"])) {
        $file = $_FILES["file"];

        // check for upload errors
        if ($file["error"] === UPLOAD_ERR_OK) {

            // TODO: Implement file type check, size limit, and content validation

            $filePath = $file["tmp_name"];
            $fileContent = file_get_contents($filePath);

            // print string to log for further processing
            file_put_contents("debug.log", $fileContent);

            // TODO: process data here

            echo json_encode(["success" => true, "message" => "file uploaded successfully"]);
        } else {
            // TODO: handle error case
            echo json_encode(["success" => false, "message" => "file upload error"]);
        }
    } else {
        // file not found in request
        echo json_encode(["success" => false, "message" => "no file received"]);
    }
}