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

        // reset debug.log to have only current file information
        file_put_contents("debug.log", "FILE RECEIVED:\n");

        // check for upload errors
        if ($file["error"] === UPLOAD_ERR_OK) {
            // second layer of security file validation
            $maxSize = 5 * 1024 * 1024;

            // check file size
            if ($file["size"] > $maxSize) {
                echo json_encode(["success" => false, "message" => "File to large. Max size 5MB."]);
                exit();
            }
            file_put_contents("debug.log", "Filesize: " . $file["size"] . " Bytes\n", FILE_APPEND);

            // Regex for file name validation
            $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
            $dirTraversalRegex = '/\.\./';
            $fileName = $file["name"];

            // check for invalid Chars on file name
            if (preg_match($invalCharsRegex, $fileName) || preg_match($dirTraversalRegex, $fileName)) {
                echo json_encode(["success" => false, "message" => "Filename contains invalid characters. Please Rename your csv and try again."]);
                exit();
            }
            file_put_contents("debug.log", "Filename: " . $file["name"] . "\n", FILE_APPEND);

            // MIME type validation
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file["tmp_name"]);
            if ($mimeType !== "text/csv") {
                echo json_encode(["success" => false, "message" => "Invalid file type. Please select CSV file."]);
                exit();
            }
            file_put_contents("debug.log", "MIME type: " . $mimeType . "\n", FILE_APPEND);

            $fileContent = file_get_contents($file["tmp_name"]);
            file_put_contents("debug.log", $fileContent, FILE_APPEND);

            // TODO: process data to create SQL Table here
            // validate each content row for expected format, data type, data integrity, sanitization

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