<?php

// Handle CORS
// origin "*" only for dev and demo not for production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// TODO: included classes
include_once "../classes/upload-contr.class.php";

// Handle preflight request for OPTIONS method from browser
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // check if file is set in superglobal
    if (isset($_FILES["file"])) {
        $file = $_FILES["file"];

        // reset debug.log to have only current file information
        file_put_contents("debug.log", "FILE RECEIVED:\n");

        // check for upload errors
        if ($file["error"] === UPLOAD_ERR_OK) {

            // instantiate class for second layer of security
            $upload = new UploadContr($file);
            $result = $upload->validateFile();

            // TODO: move to controller
            $fileContent = file_get_contents($file["tmp_name"]);
            file_put_contents("debug.log", $fileContent, FILE_APPEND);

            echo json_encode($result);

        } else {
            // TODO: handle error case
            echo json_encode(["success" => false, "message" => "file upload error"]);
        }
    } else {
        // file not found in request
        echo json_encode(["success" => false, "message" => "no file received"]);
    }
}