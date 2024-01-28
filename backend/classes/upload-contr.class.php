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

        if (!$this->validateFileFormat($this->file)) {
            echo json_encode(["success" => false, "message" => "File format corrupted."]);
            exit();
        }


        // TODO: process data to create SQL Table here
        // validate each content row for expected data type, data integrity, sanitization

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
        if ($mimeType !== "text/csv" && $mimeType !== "text/plain") {
            return false;
        }
        file_put_contents("debug.log", "MIME type: " . $mimeType . "\n", FILE_APPEND);
        return true;
    }

    private function validateFileFormat($file)
    {
        // SIDENOTES:
        // - in case of problems separating data regarding line endings with final csv consider use of CSV Parser to check format

        $delimiter = ";"; // define delimiter for value separation (in this case I assume it is ";")
        $fileContent = file_get_contents($file["tmp_name"]); // read file content
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $fileContent); // normalize line endings

        $temp = tmpfile(); // creating temp file handle for fgetcsv method
        fwrite($temp, $normalizedContent); // write normalized content to temp file
        fseek($temp, 0); // reset file pointer from end to start

        // read headers (assuming first row are headers due to convention)
        $headers = fgetcsv($temp, 0, $delimiter);
        if ($headers === false) {
            fclose($temp);
            return false; // unable to read headers
        }

        // validate each row
        while (($row = fgetcsv($temp, 0, $delimiter)) !== FALSE) {

            if (count($row) != count($headers)) {
                fclose($temp);
                return false; // row does not match header count
            }
        }

        fclose($temp);
        file_put_contents("debug.log", $fileContent, FILE_APPEND);
        return true;
    }
}