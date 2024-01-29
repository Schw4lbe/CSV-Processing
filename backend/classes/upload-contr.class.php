<?php

class UploadContr extends Upload
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function csvUpload()
    {
        // file validations
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

        // table creation and data import
        $createTableResult = parent::createTable($this->file);
        if ($createTableResult["success"]) {
            $insertDataResult = parent::insertData($createTableResult["tableName"], $this->file);

            if ($insertDataResult["success"]) {
                return ["success" => true, "message" => "Successfully uploaded data and created table."];
            } else {
                return ["success" => false, "message" => "Table created, failed inserting data."];
            }
        } else {
            return ["success" => false, "message" => "Failed to create table."];
        }
    }

    private function validateFileSize($file)
    {
        $maxSize = 5 * 1024 * 1024;
        // check File size
        if ($file["size"] > $maxSize) {
            return false;
        }
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
        return true;
    }

    private function validateFileFormat($file)
    {
        $delimiter = ";"; // define delimiter for value separation (in this case I assume it is ";")
        $fileContent = file_get_contents($file["tmp_name"]); // read file content
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $fileContent); // normalize line endings

        $temp = tmpfile(); // creating temp file handle for fgetcsv method
        fwrite($temp, $normalizedContent); // write normalized content to temp file
        rewind($temp); // reset file pointer from end to start

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
        return true;
    }
}