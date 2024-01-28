<?php

class UploadContr
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function validateFile()
    {
        if (!$this->validateFileSize($this->file)) {
            echo json_encode(["success" => false, "message" => "File to large. Max size 5MB."]);
            exit();
        }

        if (!$this->validateFileName($this->file)) {
            echo json_encode(["success" => false, "message" => "Filename contains invalid characters. Please rename your file and try again."]);
            exit();
        }

        if (!$this->validateFileType($this->file)) {
            echo json_encode(["success" => false, "message" => "Invalid file type. Please select CSV file."]);
            exit();
        }

        // TODO: process data to create SQL Table here
        // validate each content row for expected format, data type, data integrity, sanitization

        return ["success" => true, "message" => "file uploaded successfully"];
    }

    private function validateFileSize($file)
    {
        $maxSize = 5 * 1024 * 1024;
        // check File size
        if ($file["size"] > $maxSize) {
            return false;
        }
        file_put_contents("debug.log", "Filesize: " . $file["size"] . " Bytes\n", FILE_APPEND);
        return true;
    }

    private function validateFileName($file)
    {
        // Regex for file name validation
        $invalCharsRegex = '/[\/:*?"<>|\\\\]/';
        $dirTraversalRegex = '/\.\./';
        $fileName = $file["name"];

        // check for invalid Chars in file name
        if (preg_match($invalCharsRegex, $fileName) || preg_match($dirTraversalRegex, $fileName)) {
            return false;
        }
        file_put_contents("debug.log", "Filename: " . $file["name"] . "\n", FILE_APPEND);
        return true;
    }

    private function validateFileType($file)
    {
        // MIME type validation
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file["tmp_name"]);
        if ($mimeType !== "text/csv") {
            return false;
        }
        file_put_contents("debug.log", "MIME type: " . $mimeType . "\n", FILE_APPEND);
        return true;
    }
}